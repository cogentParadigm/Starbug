<?php
namespace Starbug\Db\Query\Traits;

trait Mode {
	protected $mode = "select";

	public function getMode() {
		return $this->mode;
	}

	public function setMode($mode) {
		$this->mode = $mode;
	}

	public function isSelect() {
		return ($this->mode == "select");
	}

	public function isInsert() {
		return ($this->mode == "insert");
	}

	public function isUpdate() {
		return ($this->mode == "update");
	}

	public function isDelete() {
		return ($this->mode == "delete");
	}

	public function isTruncate() {
		return ($this->mode == "truncate");
	}
}
