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
		//TODO: validate prices, lines could be stale
		$lines = $this->query("lines")->condition("orders_id", $order['id'])->select("SUM(CASE WHEN type='coupon' THEN -1*price ELSE price END*qty) as total")->one();
		$total = $lines['total'];
		$ammend = array("id" => $order['id'], "email" => $payment['email'], "phone" => $payment['phone'], "total" => $total);
		if ($this->user->loggedIn()) $ammend['owner'] = $this->user->userinfo('id');
		$this->store($ammend);
		$payment['amount'] = $total/100;
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
		$this->payments->create($payment, $order);
		if (!$this->errors()) {
			$this->store(array("id" => $order['id'], "order_status" => "pending"));
		}
	}

}
?>
