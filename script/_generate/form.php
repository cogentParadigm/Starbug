<?php
if (file_exists(dirname(__FILE__)."/../../core/db/schema/".ucwords($argv[2]))) {
	$fields = unserialize(file_get_contents(dirname(__FILE_)."/../../core/db/schema/".ucwords($argv[2])));
	foreach ($fields as $fieldname => $formfield) {
		if (!isset($formfield['input_type'])) if ($formfield['type'] != 'password') $fields[$fieldname]['type'] = 'text';
		else {
			$fields[$fieldname]['type'] = $formfield['input_type'];
		}
	}
}
include(dirname(__FILE__)."/../../util/Form.php");
$data = Form::render($fields);
$file = fopen(dirname(__FILE__)."/../../app/elements/".ucwords($argv[2])."_form.php", "w");
fwrite($file, $data);
fclose($file);
?>