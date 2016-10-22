<?php
namespace Starbug\Payment;
use Starbug\Core\SubscriptionsModel;
class Subscriptions extends SubscriptionsModel {

	function update($subscription) {
		if (empty($subscription["card"])) {
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
