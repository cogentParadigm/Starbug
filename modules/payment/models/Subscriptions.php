<?php
namespace Starbug\Payment;
use Starbug\Core\SubscriptionsModel;
class Subscriptions extends SubscriptionsModel {

	function update($subscription) {
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
		}
	}

	function cancel($subscription) {
		$this->gateway->cancelSubscription($subscription);
	}

	function payment($data) {
		$this->update($data);
		$subscription = $this->collections->get("ExpiredSubscriptions")->one(["id" => $data["bill"]]);
		$this->gateway->processSubscription($subscription);
	}

}
?>
