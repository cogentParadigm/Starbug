<?php
class Uris {

	function query_admin($query, &$ops) {
		$query->select($query->model.".statuses.term as statuses");
		if (!logged_in("admin")) $query->action("read");
		if (!empty($ops['type'])) {
			$query->condition($query->model.".type", $ops['type']);
		}
		if (!empty($ops['status'])) $query->condition($query->model.".statuses.id", $ops['status']);
		else $query->condition($query->model.".statuses.slug", "deleted", "!=");
		efault($ops['orderby'], "modified DESC, created DESC, title DESC");
		$query->sort($ops['orderby']);
		return $query;
	}

	function query_filters($action, $query, &$ops) {
		if (!empty($ops['keywords'])) $query->search($ops['keywords'], $query->model.".title");
		return $query;
	}

}
?>
