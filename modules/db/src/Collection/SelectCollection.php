<?php
namespace Starbug\Core;

use Starbug\Db\Collection;
use Starbug\Db\DatabaseInterface;
use Starbug\Db\Schema\SchemerInterface;

class SelectCollection extends Collection {
  protected $optional = false;
  protected $schema;
  public function __construct(DatabaseInterface $db, SchemerInterface $schemer) {
    $this->db = $db;
    $this->schema = $schemer->getSchema();
  }
  public function build($query, $ops) {
    if (empty($ops['id'])) {
      $query->condition($query->model.".deleted", "0");
      if (isset($ops["optional"])) {
        $this->optional = $ops["optional"];
      }
    }
    $idSelection = $labelSelection = $query->model.".id";
    if ($this->schema->getTable($query->model)->hasOption("label_select")) {
      $labelSelection = $this->schema->getTable($query->model)->getOption("label_select");
    }
    $query->select($idSelection);
    $query->select($labelSelection." as label");
    return $query;
  }
  public function filterRows($rows) {
    if (false !== $this->optional) {
      array_unshift($rows, ["id" => "", "label" => $this->optional]);
    }
    return $rows;
  }
}
