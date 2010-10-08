<?php
foreach($args as $field => $md5) {
if (!empty($fields[$field])) $fields[$field] = md5($fields[$field]);
}
?>
