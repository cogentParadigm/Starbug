<?php
namespace Starbug\Content;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
class ApiTagsController extends ApiController {
	public $model = "tags";
	function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function admin() {
		$this->api->render("AdminTags");
	}
	function select() {
		$this->api->render("Select");
	}
	function filterQuery($collection, $query, &$ops) {
		if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
		return $query;
	}
}
?>
