<?php
namespace Starbug\Db\Query;

interface HistoryInterface {
  public function log($name, $args = []);
  public function setDirty($dirty = true);
  public function getDirty();
}
