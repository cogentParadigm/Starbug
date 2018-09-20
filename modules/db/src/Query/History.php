<?php
namespace Starbug\Db\Query;

class History implements HistoryInterface {
  protected $dirty = true;
  protected $tag = "default";
  protected $operations = [];

  public function log($name, $args = []) {
    $args["operation"] = $name;
    $args['tag'] = $this->tag;
    $this->operations[$this->tag][] = $args;
    $this->setDirty(true);
  }

  public function setDirty($dirty = true) {
    $this->dirty = $dirty;
  }

  public function getDirty() {
    return $this->dirty;
  }
}
