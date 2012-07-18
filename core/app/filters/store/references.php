<?php
foreach($args as $field => $references) {
	if (($inserting) && (empty($fields[$field]))) $fields[$field] = $this->model(reset(explode(" ", $references)))->insert_id;
	else if (!$storing) $on_store = true;
}
?>
