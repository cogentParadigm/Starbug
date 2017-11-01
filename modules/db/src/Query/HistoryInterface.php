<?php
namespace Starbug\Db\Query;
interface HistoryInterface {
	public function log($name, $args = array());
	public function setDirty($dirty = true);
	public function getDirty();
}
