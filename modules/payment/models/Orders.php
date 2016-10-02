<?php
namespace Starbug\Payment;
use Starbug\Core\OrdersModel;
class Orders extends OrdersModel {

	function create($order) {
		if (empty($order["id"])) {
			$order["token"] = $this->request->getCookie("cid");
		}
		$this->store($order);
	}

}
?>
