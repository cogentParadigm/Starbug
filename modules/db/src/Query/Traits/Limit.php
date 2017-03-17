<?php
namespace Starbug\Db\Query\Traits;

trait Limit {
	protected $limit = false;
	protected $skip = 0;

	public function setLimit($limit) {
		if (false !== strpos($limit, ",")) {
			list($skip, $limit) = explode(",", $limit);
			$this->setSkip($skip);
		}
		$this->limit = trim($limit);
	}

	public function getLimit() {
		return $this->limit;
	}

	public function setSkip($skip) {
		$this->skip = trim($skip);
	}

	public function getSkip() {
		return $this->skip;
	}
}
