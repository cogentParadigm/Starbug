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

	function payment($payment) {
		if (empty($payment['id'])) {
			$order = $this->cart->getOrder();
		} else {
			$order = $this->load($payment['id']);
		}
		$this->request->setPost('orders', 'id', $order['id']);

		//populate the billing address
		$address = $this->query("address")->condition("address.id", $order['billing_address'])->select("address.*,address.country.name as country")->one();
		$payment['country'] = $address['country'];
		$payment['address'] = $address['address1'];
		$payment['address2'] = $address['address2'];
		$payment['zip'] = $address['postal_code'];
		$payment['city'] = $address['locality'];
		$payment['state'] = $address['administrative_area'];
		if (is_numeric($payment['state'])) {
			$state = $this->query("provinces")->condition("id", $payment['state'])->one();
			$payment['state'] = $state['name'];
		}

		//prepare details to be added to the order
		$ammend = array("id" => $order['id'], "email" => $payment['email'], "phone" => $payment['phone']);
		if ($this->user->loggedIn()) $ammend['owner'] = $this->user->userinfo('id');

		//determine single payment amount
		//TODO: validate prices, lines could be stale
		$lines = $this->query("lines")
			->condition("orders_id", $order['id'])
			->condition("recurring", "0")
			->select("SUM(CASE WHEN type='coupon' THEN -1*price ELSE price END*qty) as total")->one();
		$total = $lines['total'];
		if ($total) {
			$ammend["total"] = $total;
			$this->payments->create($payment + ["amount" => $total/100], $order);
		}

		//determine recurring payment amounts
		$lines = $this->query("lines")
			->condition("orders_id", $order['id'])
			->condition("recurring", "1")->all();
		foreach ($lines as $line) {
			$price = $line["price"] * $line["qty"];
			$this->subscriptions->create($payment + ["amount" => $price] + $line, $order);
		}

		$this->store($ammend);
		if (!$this->errors()) {
			$this->store(array("id" => $order['id'], "order_status" => "pending"));
		}
	}

}
?>
