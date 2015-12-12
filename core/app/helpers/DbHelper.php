<?php
namespace Starbug\Core;
class DbHelper {
	public function __construct(DatabaseInterface $db) {
		$this->target = $db;
	}
	public function helper() {
		return $this->target;
	}
}
?>
