<?php
foreach($args as $field => $unique) {
	$unique_where = "";
	$unique_replacements = array($fields[$field]);
	if (!empty($unique)) {
		$unique = explode(" ", $unique);
		foreach ($unique as $u) {
			$unique_where .= " && $u=?";
			$unique_replacements[] = $fields[$u];
		}
	}
	if (is_array($from)) {
		foreach ($from as $k => $v) {
			$unique_where .= " && $k!=?";
			$unique_replacements[] = $v;
		}
	}
	$urow = $this->query($name, "select:id, $field  where:$field=?$unique_where  limit:1", $unique_replacements);
	if ($this->record_count != 0 && !empty($fields[$field])) $errors[$field]["exists"] = "That $field already exists.";
}
?>
