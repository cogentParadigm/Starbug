<?php
foreach($args as $field => $slash) {
if (!empty($fields[$field])) $fields[$field] = addslashes($fields[$field]);
}
?>
