<?php
namespace Starbug\Db\Query\Traits;

trait Group {
	protected $group = [];

	public function addGroup($column) {
		$this->group[$column] = 1;
	}

	public function getGroup() {
		return $this->group;
	}
}
