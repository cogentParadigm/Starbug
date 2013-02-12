<?php
foreach($args as $field => $novalue) {
if (!empty($fields[$field])) $fields[$field] = Session::hash_password($fields[$field]);
}
?>
