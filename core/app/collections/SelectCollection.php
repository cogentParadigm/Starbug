<?php
namespace Starbug\Core;

class SelectCollection extends Collection {
  protected $optional = false;
  public function build($query, &$ops) {
    $query->removeSelection();
    if (empty($ops['id'])) {
      $query->condition($query->model.".deleted", "0");
      if (isset($ops["optional"])) {
        $this->optional = $ops["optional"];
      }
    }
    $query->select($query->model.".id");
    $query->select($this->models->get($query->model)->label_select." as label");
    return $query;
  }
  public function filterRows($rows) {
    if (false !== $this->optional) {
      array_unshift($rows, ["id" => "", "label" => $this->optional]);
    }
    return $rows;
  }
}
