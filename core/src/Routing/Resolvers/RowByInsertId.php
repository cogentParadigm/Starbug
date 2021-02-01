<?php
namespace Starbug\Core\Routing\Resolvers;

use Starbug\Core\DatabaseInterface;

class RowByInsertId {
  public function __invoke(DatabaseInterface $db, $model) {
    if ($id = $db->getInsertId($model)) {
      return $db->get($model, $id);
    }
    return null;
  }
}
