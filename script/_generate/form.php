<?php
/**
* FILE: script/_generate/form.php
* PURPOSE: generates a form using the form generation utility
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
include(dirname(__FILE__)."/../../etc/Etc.php");
include(dirname(__FILE__)."/../../util/Args.php");
$args = new Args();
$meth = "post";
$fields = unserialize(file_get_contents(dirname(__FILE__)."/../../core/db/schema/".$argv[2]));
if ($args->flag('s')) $fields['security'] = array("input_type" => "select", "range" => "0:".Etc::SUPER_ADMIN_SECURITY, "default" => Etc::DEFAULT_SECURITY);
foreach ($fields as $fieldname => $formfield) {
	if ($formfield["type"] == "datetime") {
		$fields[$fieldname]["type"] = "date_select";
		$fields[$fieldname]["time_select"] = true;
	}
	if (isset($fields[$fieldname]["input_type"])) $fields[$fieldname]["type"] = $formfield["input_type"];
	else if ($fields[$fieldname]['type'] != 'password') $fields[$fieldname]["type"] = "text";
	if ($args->flag('f')) $fields[$fieldname]['field']['type'] = $args->flag('f');
	if ($fields[$fieldname]['type'] == 'bin') $meth ="mult";
}
include(dirname(__FILE__)."/../../util/Form.php");
$data = Form::render($fields, strtolower($argv[2]), $meth);
$file = fopen($base.$argv[2]."/".strtolower($argv[2])."_form.php", "w");
fwrite($file, $data);
fclose($file);
?>
