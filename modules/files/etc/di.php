<?php
namespace Starbug\Files;

use function DI\add;
use function DI\get;
use function DI\autowire;
use Psr\Container\ContainerInterface;
use Starbug\Files\Storage\Filesystem;
use Starbug\Files\Storage\MountManager;
use Starbug\Files\Storage\Adapter\Local;
use Starbug\Files\Storage\Adapter\LocalPrivate;
use Starbug\Files\Command\CleanupDatabase;
use Starbug\Files\Command\CleanupFilesystem;
use Starbug\Files\Helper\FilesystemsHelper;
use Starbug\Files\Query\Hook\StoreUploadHook;
use Starbug\Http\UriBuilder;

return [
  'Starbug\\Files\\ImagesInterface' => autowire('Starbug\\Files\\Images')
    ->constructorParameter('base_directory', get('base_directory')),
  "db.query.executor.hooks" => add([
    "upload" => StoreUploadHook::class
  ]),
  "route.providers" => add([
    get("Starbug\Files\RouteProvider")
  ]),
  "template.helpers" => add([
    "filesystems" => FilesystemsHelper::class
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
  'filesystems.instances' => function (ContainerInterface $c) {
    $filesystems = [];
    foreach ($c->get("filesystem.adapters") as $prefix) {
      $adapter = $c->get('filesystem.adapter.'.$prefix);
      $config = $c->has('filesystem.config.'.$prefix) ? $c->get('filesystem.config.'.$prefix) : [];
      if (is_string($adapter)) {
        $adapter = $c->get("filesystem.adapter.".$adapter);
      }
      $filesystems[$prefix] = new Filesystem($adapter, $config);
    }
    return $filesystems;
  },
  MountManager::class => function (ContainerInterface $c) {
    return new MountManager($c->get("filesystems.instances"));
  },
  "League\Flysystem\MountManager" => get(MountManager::class),
  'Starbug\Files\Storage\FilesystemInterface' => function (ContainerInterface $c) {
    return $c->get("filesystems.instances")["default"];
  },
  'db.schema.migrations' => add([
    get('Starbug\Files\Migration')
  ]),
  "Starbug\Files\*Interface" => autowire("Starbug\Files\*"),
  "scripts.files-cleanup-filesystem" => CleanupFilesystem::class,
  "scripts.files-cleanup-database" => CleanupDatabase::class
];
