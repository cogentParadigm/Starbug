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
		$order_total = 0;
		$ammend = array("id" => $order['id'], "email" => $payment['email'], "phone" => $payment['phone']);
		if ($this->user->loggedIn()) $ammend['owner'] = $this->user->userinfo('id');

		//determine single payment amount
		//TODO: validate prices, lines could be stale
		$lines = $this->query("product_lines")
			->condition("orders_id", $order['id'])
			->condition("product_lines.product.payment_type", "single")
			->select("SUM(product_lines.price * qty) as total")->one();
		$total = $lines['total'];
		if ($total) {
			$order_total += $total;
			$ammend["total"] = $total;
			$this->purchase($payment + ["amount" => $total, "orders_id" => $order["id"]]);
		}

		//determine recurring payment amounts
		$lines = $this->query("product_lines")
			->condition("orders_id", $order['id'])
			->condition("product_lines.product.payment_type", "recurring")->all();
		foreach ($lines as $line) {
			$price = $line["price"] * $line["qty"];
			$order_total += $price;
			$this->purchase($payment + ["amount" => $price, "orders_id" => $order["id"]]);
			if (!$this->errors()) {
				$this->subscriptions->createSubscription(["orders_id" => $order["id"], "amount" => $price, "product" => $line["product"], "payment" => $this->models->get("payments")->insert_id] + $payment);
			}
		}

		$this->store($ammend);
		if (!$this->errors()) {
			$this->store(array("id" => $order['id'], "order_status" => "pending"));
			$lines = $this->query("product_lines")->condition("orders_id", $order["id"])->all();
			$order["description"] = $lines[0]["description"];
			$count = count($lines) - 1;
			if ($count > 1) {
				$order["description"] .= " and ".$count." other items";
			} else if ($count > 0) {
				$order["description"] .= " and 1 other item";
			}
			$rows = [];
			foreach ($lines as $line) {
				$rows[] = "<tr><td>".$line["description"]."</td><td>".$line["qty"]."</td><td>".$this->priceFormatter->format($line["price"]*$line["qty"])."</td></tr>";
			}
			$order["details"] = implode("\n", [
				"<p>Order #".$order["id"]."</p>",
				"<table>",
				"<tr><th>Product</th><th>Qty</th><th>Total</th></tr>",
				implode("\n", $rows),
				"</table>",
				"<p><strong>Order Total:</strong> ".$this->priceFormatter->format($order_total)."</p>"
			]);
			$data = [
				"user" => $this->user->getUser(),
				"order" => $order
			];
			$this->mailer->send(["template" => "Order Confirmation", "to" => $payment["email"]], $data);
		}
	}

	protected function purchase($payment) {
		if (empty($payment["card"])) {
			$this->gateway->purchase($payment);
		} else {
			$this->subscriptions->purchase($payment);
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
