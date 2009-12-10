<?php
//default
foreach($args as $field => $default) if ($fields[$field] == "") {
	$fields[$field] = $default;
	unset($errors[$field]["required"]);
}
?>
