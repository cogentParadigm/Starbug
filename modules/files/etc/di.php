<?php
namespace Starbug\Files;

use Psr\Container\ContainerInterface;
use League\Flysystem\MountManager;
use Starbug\Core\Storage\Filesystem;
use Starbug\Core\Storage\Adapter\Local;
use Starbug\Core\Storage\Adapter\LocalPrivate;
use Starbug\Http\Url;
use DI;

return [
  'routes' => DI\add([
    "upload" => [
      "title" => "Starbug\Files\UploadController",
      "controller" => "upload",
      "template" => "xhr.xhr",
      "groups" => "user"
    ],
    "files/download" => [
      "title" => "Download Private File",
      "controller" => "Starbug\Files\DownloadController",
      "action" => "download",
      "groups" => ["admin"]
    ]
  ]),
  'filesystem.adapters' => ['default', 'public', 'private', 'tmp'],
  'filesystem.adapter.default' => 'public',
  'filesystem.public' => 'var/public/uploads',
  'filesystem.private' => 'var/private/uploads',
  'filesystem.tmp' => 'var/tmp',
  'filesystem.adapter.public' => function (ContainerInterface $c) {
    $here = $c->get("Starbug\Http\UrlInterface");
    $public = $c->get("filesystem.public");
    $url = (new Url($here->getHost(), $here->getDirectory().$public."/"))->setScheme($here->getScheme());
    $adapter = new Local($c->get("base_directory")."/".$public);
    $adapter->setUrlInterface($url);
    return $adapter;
  },
  "filesystem.adapter.private" => function (ContainerInterface $c) {
    $here = $c->get("Starbug\Http\UrlInterface");
    $private = $c->get("filesystem.private");
    $url = (new Url($here->getHost(), $here->getDirectory()."files/download/"))->setScheme($here->getScheme());
    $adapter = new LocalPrivate($c->get("base_directory")."/".$private);
    $adapter->setUrlInterface($url);
    return $adapter;
  },
  'filesystem.adapter.tmp' => function (ContainerInterface $c) {
    $here = $c->get("Starbug\Http\UrlInterface");
    $tmp = $c->get("filesystem.tmp");
    $url = (new Url($here->getHost(), $here->getDirectory().$tmp."/"))->setScheme($here->getScheme());
    $adapter = new Local($c->get("base_directory")."/".$tmp);
    $adapter->setUrlInterface($url);
    return $adapter;
  },
  'League\Flysystem\MountManager' => function (ContainerInterface $c) {
    $manager = new MountManager();
    $adapters = $c->get("filesystem.adapters");
    foreach ($adapters as $prefix) {
      $adapter = $c->get('filesystem.adapter.'.$prefix);
      $config = $c->has('filesystem.config.'.$prefix) ? $c->get('filesystem.config.'.$prefix) : [];
      if (is_string($adapter)) $adapter = $c->get("filesystem.adapter.".$adapter);
      $manager->mountFilesystem($prefix, new Filesystem($adapter, $config));
    }
    return $manager;
  },
  'Starbug\Core\Storage\FilesystemInterface' => function (ContainerInterface $c) {
    $manager = $c->get("League\Flysystem\MountManager");
    return $manager->getFilesystem("default");
  },
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Files\Migration')
  ])
];
