<?php
include(dirname(__FILE__)."/../../etc/Etc.php");
include(dirname(__FILE__)."/../../util/Args.php");
$args = new Args();
$meth = "post";
if (file_exists(dirname(__FILE__)."/../../core/db/schema/".ucwords($argv[2]))) {
	$fields = unserialize(file_get_contents(dirname(__FILE__)."/../../core/db/schema/".ucwords($argv[2])));
	if ($args->flag('s')) $fields['security'] = array("input_type" => "select", "range" => "0:".Etc::SUPER_ADMIN_SECURITY, "default" => "2");
	foreach ($fields as $fieldname => $formfield) {
		if (isset($formfield["input_type"])) $fields[$fieldname]["type"] = $formfield["input_type"];
		else if ($fields[$fieldname]['type'] != 'password') $fields[$fieldname]["type"] = "text";
		if ($args->flag('f')) $fields[$fieldname]['field']['type'] = $args->flag('f');
		if ($fields[$fieldname]['type'] == 'bin') $meth ="mult";
	}
}
include(dirname(__FILE__)."/../../util/Form.php");
$data = Form::render($fields, strtolower($argv[2]), $meth);
$file = fopen(dirname(__FILE__)."/../../app/elements/".strtolower($argv[2])."_form.php", "w");
fwrite($file, $data);
fclose($file);
?>