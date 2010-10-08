<?php
if (!empty($args['keywords'])) {
	$this->import("util/search");
	$args['where'] = ((empty($args['where'])) ? "" : $args['where']." && ").keywordClause($args['keywords'], split(",", $args['search']));
}
?>