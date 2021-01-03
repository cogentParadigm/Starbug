<?php
namespace Starbug\Content;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Routing\AliasStorageInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Http\UriBuilderInterface;

class AliasStorage implements AliasStorageInterface {
  public function __construct(DatabaseInterface $db, UriBuilderInterface $uri) {
    $this->db = $db;
    $this->uri = $uri;
  }
  public function addAlias($alias, $path) {
    $this->db->store("aliases", ["path" => $path, "alias" => $alias]);
  }
  public function addAliases($aliases) {
    foreach ($aliases as $alias => $path) {
      $this->addAlias($alias, $path);
    }
  }
  public function getPath(ServerRequestInterface $request) {
    $path = $this->uri->relativize($request->getUri())->getPath();
    $query = $this->db->query("aliases")->condition("alias", $path);
    if ($path = $query->one()) {
      return $path["path"];
    }
    return false;
  }
}
