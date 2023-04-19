<?php
namespace Starbug\Files;

use function DI\add;
use function DI\get;
use function DI\autowire;
use Psr\Container\ContainerInterface;
use League\Flysystem\MountManager;
use Starbug\Core\Storage\Filesystem;
use Starbug\Core\Storage\Adapter\Local;
use Starbug\Core\Storage\Adapter\LocalPrivate;
use DI;
use Starbug\Http\UriBuilder;

return [
  "route.providers" => add([
    get("Starbug\Files\RouteProvider")
  ]),
  'filesystem.adapters' => ['default', 'public', 'private', 'tmp'],
  'filesystem.adapter.default' => 'public',
  'filesystem.public' => 'var/public/uploads',
  'filesystem.private' => 'var/private/uploads',
  'filesystem.tmp' => 'var/tmp',
  'filesystem.adapter.public' => function (ContainerInterface $c) {
    $base = $c->get("Starbug\Http\UriBuilderInterface")->getBaseUri();
    $public = $c->get("filesystem.public");
    $path = $base->getPath().$public."/";
    $adapter = new Local($c->get("base_directory")."/".$public);
    $builder = new UriBuilder($base->withPath($path));
    $adapter->setUriBuilder($builder);
    return $adapter;
  },
  "filesystem.adapter.private" => function (ContainerInterface $c) {
    $base = $c->get("Starbug\Http\UriBuilderInterface")->getBaseUri();
    $private = $c->get("filesystem.private");
    $path = $base->getPath()."files/download/";
    $adapter = new LocalPrivate($c->get("base_directory")."/".$private);
    $builder = new UriBuilder($base->withPath($path));
    $adapter->setUriBuilder($builder);
    return $adapter;
  },
  'filesystem.adapter.tmp' => function (ContainerInterface $c) {
    $base = $c->get("Starbug\Http\UriBuilderInterface")->getBaseUri();
    $tmp = $c->get("filesystem.tmp");
    $path = $base->getPath().$tmp."/";
    $adapter = new Local($c->get("base_directory")."/".$tmp);
    $builder = new UriBuilder($base->withPath($path));
    $adapter->setUriBuilder($builder);
    return $adapter;
  },
  'League\Flysystem\MountManager' => function (ContainerInterface $c) {
    $manager = new MountManager();
    $adapters = $c->get("filesystem.adapters");
    foreach ($adapters as $prefix) {
      $adapter = $c->get('filesystem.adapter.'.$prefix);
      $config = $c->has('filesystem.config.'.$prefix) ? $c->get('filesystem.config.'.$prefix) : [];
      if (is_string($adapter)) {
        $adapter = $c->get("filesystem.adapter.".$adapter);
      }
      $manager->mountFilesystem($prefix, new Filesystem($adapter, $config));
    }
    return $manager;
  },
  'Starbug\Core\Storage\FilesystemInterface' => function (ContainerInterface $c) {
    $manager = $c->get("League\Flysystem\MountManager");
    return $manager->getFilesystem("default");
  },
  'db.schema.migrations' => add([
    get('Starbug\Files\Migration')
  ]),
  "Starbug\Files\*Interface" => autowire("Starbug\Files\*")
];
