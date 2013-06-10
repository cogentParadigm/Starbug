<?php
dfault($args['keywords'], $_GET['keywords']);
if (!empty($args['keywords'])) {
	//split tokens
	$preliminary_search_fields = preg_split('~(?<!\\\)' . preg_quote(",", '~') . '~', $args['search']);
 	foreach ($preliminary_search_fields as $sfk => $sfv) $preliminary_search_fields[$sfk] = str_replace("\,", ",", $sfv);
	//pull out virtual columns
	$search_fields = array();
	foreach ($preliminary_search_fields as $s => $f) {
		$col_name = (false === strpos($f, ".")) ? $f : end(explode(".", $f));
		if (!$this->has($schema['fields'][$col_name]['type'])) $search_fields[] = $f;
	}
	//append search conditions
	$args['where'] = ((empty($args['where'])) ? "" : $args['where']." && ").$this->search_clause($args['keywords'], $search_fields);
}
?>
