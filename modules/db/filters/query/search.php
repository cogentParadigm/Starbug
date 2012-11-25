<?php
dfault($args['keywords'], $_GET['keywords']);
if (!empty($args['keywords'])) {
	//pull out virtual columns
	$preliminary_search_fields = explode(",", $args['search']);
	$search_fields = array();
	foreach ($preliminary_search_fields as $s => $f) {
		if (!$this->has($schema['fields'][$f]['type'])) $search_fields[] = $f;
	}
	//append search conditions
	$args['where'] = ((empty($args['where'])) ? "" : $args['where']." && ").$this->search_clause($args['keywords'], $search_fields);
}
?>
