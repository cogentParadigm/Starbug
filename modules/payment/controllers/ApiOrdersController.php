<?php
namespace Starbug\Payment;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
class ApiOrdersController extends ApiController {
	public $model = "orders";
	function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function admin() {
		$this->api->render("AdminOrders");
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
