<?php
namespace Starbug\Payment;
use Starbug\Core\SubscriptionsModel;
class Subscriptions extends SubscriptionsModel {

	function update($subscription, $email = true) {
		if (empty($subscription["card"])) {
			//populate the billing address
			$address = $this->query()->condition("subscriptions.id", $subscription["id"])
				->select("subscriptions.orders_id.email as email")
				->select(["*", "country.name as country"], "subscriptions.orders_id.billing_address")->one();
			$subscription['email'] = $address['email'];
			$subscription['country'] = $address['country'];
			$subscription['address'] = $address['address1'];
			$subscription['address2'] = $address['address2'];
			$subscription['zip'] = $address['postal_code'];
			$subscription['city'] = $address['locality'];
			$subscription['state'] = $address['administrative_area'];
			if (is_numeric($subscription['state'])) {
				$state = $this->query("provinces")->condition("id", $subscription['state'])->one();
				$subscription['state'] = $state['name'];
			}
			$card = $this->gateway->createCard($subscription);
			$subscription["card"] = $card["id"];
		}
		if (!$this->errors()) {
			$this->gateway->updateSubscription($subscription);
			if ($email && !$this->db->errors()) {
				$subscription = $this->query()->condition("subscriptions.id", $subscription["id"])->select("subscriptions.product.name as description")
					->select(["brand", "number", "month", "year"], "subscriptions.card")->one();
				$bill = $this->query("bills")->condition("subscriptions_id", $subscription["id"])->sort("due_date DESC")->one();
				$subscription["details"] = implode("\n", [
					"<p><strong>Card:</strong>".$subscription["brand"]." xxxx".$subscription["number"]."</p>",
					"<p><strong>Next payment date:</strong> ".date("l, F j", strtotime($bill["scheduled_date"]))."</p>"
				]);
				$data = [
					"user" => $this->user->getUser(),
					"subscription" => $subscription
				];
				$this->mailer->send(["template" => "Update Subscription", "to" => $subscription["email"]], $data);
			}
		}
	}

	function cancel($subscription) {
		$this->gateway->cancelSubscription($subscription);
	}

	function payment($data) {
		$this->update($data, false);
		$subscription = $this->collections->get("ExpiredSubscriptions")->one(["id" => $data["bill"]]);
		$this->gateway->processSubscription($subscription);
		if (!$this->db->errors()) {
			$subscription = $this->query()->condition("subscriptions.id", $data["id"])->select("subscriptions.product.name as description,subscriptions.orders_id.email as email")
				->select(["brand", "number", "month", "year"], "subscriptions.card")->one();
			$bill = $this->query("bills")->condition("subscriptions_id", $data["id"])->sort("due_date DESC")->one();
			$subscription["details"] = implode("\n", [
				"<p><strong>Card:</strong>".$subscription["brand"]." xxxx".$subscription["number"]."</p>",
				"<p><strong>Payment amount:</strong>".$this->priceFormatter->format($subscription["amount"])."</p>",
				"<p><strong>Next payment date:</strong> ".date("l, F j", strtotime($bill["scheduled_date"]))."</p>"
			]);
			$data = [
				"user" => $this->user->getUser(),
				"payment" => $subscription
			];
			$this->mailer->send(["template" => "Payment Confirmation", "to" => $subscription["email"]], $data);
		}
	}

}
?>
