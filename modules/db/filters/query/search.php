<?php
dfault($args['keywords'], $_GET['keywords']);
if (!empty($args['keywords'])) {
	//pull out virtual columns
	$preliminary_search_fields = explode(",", $args['search']);
	$search_fields = array();
	foreach ($preliminary_search_fields as $s => $f) {
		$col_name = (false === strpos($f, ".")) ? $f : end(explode(".", $f));
		if (!$this->has($schema['fields'][$col_name]['type'])) $search_fields[] = $col_name;
	}
	//append search conditions
	$args['where'] = ((empty($args['where'])) ? "" : $args['where']." && ").$this->search_clause($args['keywords'], $search_fields);
}
?>
