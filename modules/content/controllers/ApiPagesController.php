<?php
namespace Starbug\Content;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
class ApiPagesController extends ApiController {
	public $model = "pages";
	function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function admin() {
		$this->api->render("AdminPages");
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
