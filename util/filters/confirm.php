<?php
foreach($args as $field => $confirm) {
	if ((isset($fields[$confirm])) && ($fields[$field] != $fields[$confirm])) $errors[$field]["confirm"] = "Your $field"."s do not match";
	unset($fields[$confirm]);
	unset($errors[$confirm]['required']);
}
?>
