<?php
foreach($args as $field => $optional) {
	if ((!empty($fields['id'])) && (empty($fields[$field]))) {
		unset($fields[$field]);
		unset($errors[$field]['required']);
	}
}
?>
