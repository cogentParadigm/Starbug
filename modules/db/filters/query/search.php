<?php
dfault($args['keywords'], $_GET['keywords']);
if (!empty($args['keywords'])) {
	$args['where'] = ((empty($args['where'])) ? "" : $args['where']." && ").$this->search_clause($args['keywords'], explode(",", $args['search']));
}
?>
