<?php
foreach($args as $field => $unique) {
$urow = $this->query($name, "select:id, $field	where:$field='".$fields[$field]."'	limit:1");
if (((!empty($fields['id'])) || ($this->recordCount != 0)) && ((empty($fields['id'])) || ($this->recordCount > 1) || ($fields['id'] != $urow['id']))) $errors[$field."ExistsError"] = true;
}
?>
