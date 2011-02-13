<?php
//datetime
foreach($args as $field => $datetime) {
	if (!empty($fields[$field])) $fields[$field] = date('Y-m-d H:i:s', strtotime($fields[$field]));
}
?>
