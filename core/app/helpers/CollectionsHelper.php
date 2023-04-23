<?php
namespace Starbug\Core;

use Starbug\Db\CollectionFactoryInterface;

class CollectionsHelper {
  public function __construct(CollectionFactoryInterface $collections) {
    $this->target = $collections;
  }
  public function helper() {
    return $this->target;
  }
}
