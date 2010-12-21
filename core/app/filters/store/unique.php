<?php
foreach($args as $field => $unique) {
$urow = $this->query($name, "select:id, $field  where:$field='".$fields[$field]."'".((empty($fields['id'])) ? "" : " && id!='$fields[id]'")."  limit:1");
if ($this->record_count != 0) $errors[$field]["exists"] = "That $field already exists.";
}
?>
