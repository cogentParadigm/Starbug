<?php
namespace Starbug\Core\Routing\Resolvers;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\Routing\Route;

class RowById {
  public function __construct(DatabaseInterface $db, ServerRequestInterface $request) {
    $this->db = $db;
    $this->request = $request;
  }
  public function __invoke(Route $route, $model, $id = false) {
    $bodyParams = $this->request->getParsedBody();
    if (empty($id) && !empty($bodyParams["id"])) {
      $id = $bodyParams["id"];
    }
    $record = $this->db->get($model, $id);
    if (empty($record)) {
      $route->notFound();
    }
    return $record;
  }
}
