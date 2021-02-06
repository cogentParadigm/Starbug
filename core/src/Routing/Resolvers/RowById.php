<?php
namespace Starbug\Core\Routing\Resolvers;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\Routing\Route;

class RowById {
  public function __invoke(DatabaseInterface $db, $model, $id, Route $route) {
    $record = $db->get($model, $id);
    if (empty($record)) {
      $route->notFound();
    }
    return $record;
  }
}
