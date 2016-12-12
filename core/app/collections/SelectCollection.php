<?php
namespace Starbug\Core;
class SelectCollection extends Collection {
	public function build($query, &$ops) {
		$query->undo("select");
		if (empty($ops['id'])) {
			$query->condition($query->model.".deleted", "0");
		}
		$query->select($query->model.".id");
		$query->select($this->models->get($query->model)->label_select." as label");
		return $query;
	}
}
?>
