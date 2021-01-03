<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface AliasStorageInterface {
  public function addAlias($alias, $path);
  public function addAliases($aliases);
  public function getPath(ServerRequestInterface $request);
}
