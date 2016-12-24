<?php
namespace Starbug\Files;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
use Starbug\Core\ImagesInterface;
class ApiFilesController extends ApiController {
	public $model = "files";
	function __construct(IdentityInterface $user, ImagesInterface $images) {
		$this->user = $user;
		$this->images = $images;
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
		if (reset(explode("/", $file['mime_type'])) == "image") $this->images->thumb("app/public/uploads/".$file['id']."_".$file['filename'], ["w" => 100, "h" => 100, "a" => 1]);
		return $file;
	}
}
