<?php
namespace Starbug\Payment;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
class ApiProduct_linesController extends ApiController {
	public $model = "product_lines";
	function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function admin() {
		$this->api->render("Admin");
	}
	function select() {
		$this->api->render("Select");
	}
	function cart() {
		$this->api->render("ProductLines");
	}
	function filterQuery($collection, $query, &$ops) {
		if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) {
			$query->condition("product_lines.orders_id.token", $this->request->getCookie("cid"));
		}
		return $query;
	}
}
?>
