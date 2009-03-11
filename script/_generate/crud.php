<?php
/**
* FILE: script/_generate/crud.php
* PURPOSE: generates crud
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
include("etc/Etc.php");
include("etc/Theme.php");
include("util/Args.php");
$args = new Args();
$fields = unserialize(file_get_contents("core/db/schema/$argv[2]"));
if ($args->flag('s')) $fields['security'] = array("input_type" => "select", "range" => "0:".Etc::SUPER_ADMIN_SECURITY, "default" => Etc::DEFAULT_SECURITY);

//CREATE MODEL XML
$model = "<model name=\"$argv[2]\">\n";
foreach($fields as $name => $field) {
	$model .= "\t<field name=\"$name\"";
	$kids = "";
	foreach ($field as $k => $v) {
		if (is_array($v)) foreach ($v as $key => $val) $kids .= "\t\t<$k name=\"$key\">$val</$k>\n";
		else $model .= " $k=\"$v\"";
	}
	if ($args->flag('l') == $name) $model .= " label=\"true\""; else $model .= " display=\"true\"";
	if (empty($kids)) $model .= "/>"; else $model .= ">\n$kids\t</field>";
}
$model .= "\n</model>";

//CREATE FORM XML
$form = "<form name=\"$argv[2]\" method=\"post\"";
$fieldstring = "";
$mult = false;
foreach ($fields as $name => $field) {
	if (!isset($field['id'])) $field['id'] = $name;
	if (!isset($field['label'])) $field['label'] = str_replace("_", " ", ucwords($name));
	if (!isset($field['default'])) if (!isset($field['error'][$name])) $field['error'][$name] = "Please enter a ".$field['label'];
	if (!empty($field['unique'])) if(!isset($field['error'][$name."Exists"])) $field['error'][$name."Exists"] = "That $field[label] already exists";
	if ($field['type'] == 'file') $mult = true;
	if ($field['type'] == 'timestamp') continue;
	if ($field["type"] == "password") $fields[$name]["input_type"] = "password";
	if ($field["type"] == "datetime") $fields[$name]["input_type"] = "date_select";
	if ($field["type"] == "timestamp") $fields[$name]["input_type"] = "timestamp";
	if ($field["type"] == "bool") $fields[$name]["input_type"] = "checkbox";
	if (isset($field["input_type"])) {
		$field["type"] = $field["input_type"];
		unset($field["input_type"]);
	} else $field["type"] = "text";
	$fieldstring .= "\t<field name=\"$name\"";
	$kids = "";
	foreach ($field as $k => $v) {
		if (is_array($v)) foreach ($v as $key => $val) $kids .= "\t\t<$k name=\"$key\">$val</$k>\n";
		else $fieldstring .= " $k=\"$v\"";
	}
	if (empty($kids)) $fieldstring .= "/>"; else $fieldstring .= ">\n$kids\t</field>";
}
if ($mult) $form .= " multipart=\"true\"";
$form .= ">\n$fieldstring\n</form>";

//WRITE XML
$file = fopen("core/db/schema/$argv[2]_model.xml", "wb");
fwrite($file, $model);
fclose($file);
$file = fopen("core/db/schema/$argv[2]_form.xml", "wb");
fwrite($file, $form);
fclose($file);

//SET VARS FOR THEME
$model_name = $argv[2];
$template = ($args->flag('t')) ? $args->flag('t') : Etc::DEFAULT_TEMPLATE;
$security = ($args->flag('a')) ? $args->flag('a') : Etc::DEFAULT_SECURITY;

//INCLUDE THEME CRUD FILE
include("themes/".THEME::FOLDER."/crud/crud.php");

//CREATE DIRECTORIES
foreach ($dirs as $dir) if (!file_exists("app/nouns/$dir")) mkdir("app/nouns/$dir");

//CREATE FILES
foreach ($from_model as $stylesheet => $output) exec("saxon -o app/nouns/$output core/db/schema/$argv[2]_model.xml themes/".THEME::FOLDER."/$stylesheet");
foreach ($from_form as $stylesheet => $output) exec("saxon -o app/nouns/$output core/db/schema/$argv[2]_form.xml themes/".THEME::FOLDER."/$stylesheet");

// 6.) INSERT URI
if (!$args->flag('u')) {
	include(dirname(__FILE__)."/../../etc/init.php");
	include(dirname(__FILE__)."/../../core/db/Schemer.php");
	$schemer = new Schemer($db);
	$schemer->insert("uris", "path, template, security", "'$argv[2]', '$template', '$security'");
}
