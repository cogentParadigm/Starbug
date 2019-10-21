<?php
namespace Starbug\Core\Routing;

use Starbug\Http\RequestInterface;

interface AliasStorageInterface {
  public function addAlias($alias, $path);
  public function addAliases($aliases);
  public function getPath(RequestInterface $request);
}
