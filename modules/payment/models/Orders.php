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

	function checkout($order) {
		$target = array("id" => $this->cart->get("id"));
		if (isset($order['shipping_address'])) $target['shipping_address'] = $order['shipping_address'];
		if (isset($order['billing_address'])) $target['billing_address'] = $order['billing_address'];
		if ($target['id']) {
			$this->store($target);
		}
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
			->select("SUM(CASE WHEN type='coupon_lines' THEN -1*price ELSE price END*qty) as total")->one();
		$total = $lines['total'];
		if ($total) {
			$ammend["total"] = $total;
			$this->payments->create($order, $payment + ["amount" => $total/100]);
		}

		//determine recurring payment amounts
		$lines = $this->query("lines")
			->condition("orders_id", $order['id'])
			->condition("recurring", "1")->all();
		foreach ($lines as $line) {
			$price = $line["price"] * $line["qty"];
			$this->subscriptions->create($order, $payment + ["amount" => $price/100] + $line);
		}

		$this->store($ammend);
		if (!$this->errors()) {
			$this->store(array("id" => $order['id'], "order_status" => "pending"));
		}
	}

	public function post($action, $data = array()) {
		$this->action = $action;
		if (in_array($action, ["checkout", "payment"]) && isset($data["id"]) && $this->cart->get("id") == $data["id"]) {
			$this->$action($data);
			return true;
		} else return parent::post($action, $data);
	}

}
?>
