<?php
namespace Starbug\Core;
class ApiImportsController extends ApiController {
	public $model = "imports";
	function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function admin() {
		$this->api->render("Admin");
	}
	function select() {
		$this->api->render("Select");
	}
	function filterQuery($collection, $query, &$ops) {
		if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
		if (!empty($ops['model'])) {
			$query->condition("imports.model", $ops['model']);
		}
		return $query;
	}
}
?>
