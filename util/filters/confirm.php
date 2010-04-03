<?php
foreach($args as $field => $confirm) {
	if ($fields[$field] != $fields[$confirm]) $errors[$field]["confirm"] = "Your $field"."s do not match";
	unset($fields[$confirm]);
}
?>
