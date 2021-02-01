<?php
namespace Starbug\Core\Routing\Resolvers;

use Starbug\Core\DatabaseInterface;

class RowById {
  public function __invoke(DatabaseInterface $db, $model, $id) {
    return $db->get($model, $id);
  }
}
