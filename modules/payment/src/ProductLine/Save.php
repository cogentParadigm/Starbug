<?php
namespace Starbug\Payment\ProductLine;

use Starbug\Db\DatabaseInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save as ParentOperation;
use Starbug\Db\Query\EntityInterface;

class Save extends ParentOperation {
  protected $model = "product_lines";
  public function __construct(DatabaseInterface $db, EntityInterface $entity) {
    $this->db = $db;
    $this->entity = $entity;
  }
  public function handle(array $line, BundleInterface $state): BundleInterface {
    $line["type"] = $this->model;
    $this->entity->store($this->model, $line);
    return $this->getErrorState($state);
  }
}
