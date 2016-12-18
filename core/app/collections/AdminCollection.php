<?php
namespace Starbug\Core;
class AdminCollection extends Collection {
	public function build($query, &$ops) {
		if (isset($ops["deleted"])) {
			$query->condition($query->model.".deleted", $ops["deleted"]);
		} else {
			$query->condition($query->model.".deleted", "0");
		}
		return $query;
	}
}
