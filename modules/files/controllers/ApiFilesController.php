<?php
namespace Starbug\Files;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
use Starbug\Core\ImagesInterface;
use League\Flysystem\MountManager;
class ApiFilesController extends ApiController {
	public $model = "files";
	function __construct(IdentityInterface $user, ImagesInterface $images, MountManager $filesystems) {
		$this->user = $user;
		$this->images = $images;
		$this->filesystems = $filesystems;
	}
	function admin() {
		$this->api->render("AdminFiles");
	}
	function select() {
		$this->api->render("Select");
	}
	function filterQuery($collection, $query, &$ops) {
		if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
		return $query;
	}
	function filterRow($collection, $file) {
		if (reset(explode("/", $file['mime_type'])) == "image") {
			$file["thumbnail"] = $this->images->thumb($file["location"]."://".$file['id']."_".$file['filename'], ["w" => 100, "h" => 100, "a" => 1]);
		}
		$file["url"] = $this->filesystems->getFilesystem($file["location"])->getURL($file["id"]."_".$file["filename"]);
		return $file;
	}
}
