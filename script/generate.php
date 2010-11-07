<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP <br/>
 * @file script/generate.php generates code from XSLT stylesheets
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$help = "Usage: generate TYPE NAME [OPTIONS]\n\n";
$help .= "TYPE\tWhat to generate\n";
$help .= "    \t\tcrud - CRUD nouns\n";
$help .= "    \t\tmodel - Object model\n";
$generator = array_shift($argv);
$model = array_shift($argv);
include(BASE_DIR."/util/Args.php");
$args = new Args();
global $sb;
function get_relations($from, $to) {
	global $schemer;
	$fields = $schemer->get_table($from);
	$return = (isset($fields['id'])) ? array($from => array()) : array();
	$hook = "";
	foreach($fields as $column => $options) {
		if (isset($options['references'])) {
			$ref = explode(" ", $options['references']);
			if (0 === strpos($options['references'], $to)) $hook = $column;
			else $return[$ref[0]] = array("lookup" => $from, "ref_field" => $column);
		}
	}
	if (empty($hook)) return array();
	foreach ($return as $idx => $arr) $return[$idx]["hook"] = $hook;
	return $return;
}
if ((!empty($model)) && (isset($schemer->tables[$model]))) {
	$fields = $schemer->get_table($model);

	//CREATE MODEL XML
	$xml = "<model name=\"$model\" label=\"".ucwords($model)."\" package=\"".Etc::WEBSITE_NAME."\">\n";
	$relations = array();
	foreach($fields as $name => $field) {
		$xml .= "\t<field name=\"$name\"";
		$kids = "";
		if (!isset($field['input_type'])) {
			if ($field['type'] == "text") $field['input_type'] = "textarea";
			else if ($field['type'] == "password") $field['input_type'] = "password";
			else if ($field['type'] == "bool") $field['input_type'] = "checkbox";
			else if ($field['type'] == "datetime") $field['input_type'] = "date_select";
			else if (isset($field['upload'])) $field['input_type'] = "file";
			else $field['input_type'] = "text";
		}
		if ($field['input_type'] == "file") $xml .= " multipart=\"true\"";
		foreach ($field as $k => $v) {
			if (("references" == $k) && (false === strpos($v, $model))) {
				$ref = explode(" ", $v);
				$kids .= "\t\t<references model=\"$ref[0]\" field=\"$ref[1]\"/>\n";
			}
			if (file_exists(BASE_DIR."/app/filters/store/$k.php")) $kids .= "\t\t<filter name=\"$k\" value=\"$v\"/>\n";
			else $xml .= " $k=\"$v\"";
		}
		if ($args->flag('l') == $name) $xml .= " label=\"true\""; else $xml .= " display=\"true\"";
		if (empty($kids)) $xml .= "/>\n"; else $xml .= ">\n$kids\t</field>\n";
	}
	foreach ($schemer->tables as $table => $fields) {
		$relations = get_relations($table, $model);
		foreach ($relations as $m => $r) {
			$xml .= "\t<relation model=\"$m\" field=\"$r[hook]\"".((!empty($r['lookup'])) ? " lookup=\"$r[lookup]\" ref_field=\"$r[ref_field]\"" : "")."/>\n";
		}
	}
	$xml .= "</model>";

	//WRITE XML
	$file = fopen("var/xml/$model.xml", "wb");
	fwrite($file, $xml);
	fclose($file);
	chmod("var/xml/$model.xml", 0777);
}

//SET VARS FOR GENERATOR 
$model_name = $model;
$template = ($args->flag('t')) ? $args->flag('t') : Etc::DEFAULT_TEMPLATE;
$collective = ($args->flag('c')) ? $args->flag('c') : "2";
$dirs = array(); $generate = array(); $paths = array(); $copy = array();

//INCLUDE GENERATOR FILE
if ($args->flag('u')) include(BASE_DIR."/script/generators/$generator/update.php");
else include(BASE_DIR."/script/generators/$generator/$generator.php");

//CREATE DIRECTORIES
foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) passthru("mkdir ".BASE_DIR."/$dir");
//CREATE FILES
foreach ($generate as $stylesheet => $output) {
	$o = BASE_DIR."/$output"; //output
	$s = BASE_DIR."/var/xml/$model.xml"; //source
	$xsl = BASE_DIR."/script/generators/$stylesheet"; //xsl
	passthru(Etc::JAVA_PATH." -jar ".Etc::SAXON_PATH." -o:$o -s:$s -xsl:$xsl 2>&1");
}
//COPY FILES
foreach ($copy as $origin => $dest) passthru("cp ".BASE_DIR."/$dest ".BASE_DIR."/script/generators/$origin");
?>
