<?php
namespace Starbug\Imports\Read;

use Starbug\Db\DatabaseInterface;
use DI\FactoryInterface;
use League\Flysystem\MountManager;

class StrategyFactory implements StrategyFactoryInterface {
  public function __construct(
    protected DatabaseInterface $db,
    protected MountManager $filesystems,
    protected FactoryInterface $container
  ) {
  }
  public function create($strategy, $params = []) : StrategyInterface {
    if (!empty($params["files_id"])) {
      $params["path"] = $this->getTmpPath($params["files_id"]);
    }
    return $this->container->make($strategy, $params);
  }
  protected function getTmpPath($id) {
    $file = $this->db->query("files")->condition("id", $id)->one();
    if ($this->filesystems->has("tmp://".$file["id"]."_".$file["filename"])) {
      $this->filesystems->delete("tmp://".$file["id"]."_".$file["filename"]);
    }
    $this->filesystems->copy(
      $file["location"]."://".$file["id"]."_".$file["filename"],
      "tmp://".$file["id"]."_".$file["filename"]
    );
    $path = $this->filesystems
      ->getFilesystem("tmp")
      ->getAdapter()
      ->applyPathPrefix($file["id"]."_".$file["filename"]);
    return $path;
  }
}
