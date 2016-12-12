<?php
namespace Starbug\Payment;
use Starbug\Core\ModelFactoryInterface;
class PaymentSubscription implements PaymentSubscriptionInterface {
	public function __construct(ModelFactoryInterface $models, Authnet $authnet) {
		$this->models = $models;
		$this->authnet = $authnet;
	}
	protected function format($subscription) {
		foreach (array('length' => 'interval', 'total_occurrences' => 'occurrences', 'trial_occurrences' => 'trials') as $dest => $source) {
			if (empty($subscription[$dest]) && !empty($subscription[$source])) $subscription[$dest] = $subscription[$source];
		}
		return $subscription;
	}
	protected function save($type, $order, $subscription) {
		$record = ["orders_id" => $order["id"], "type" => $type];
		if ($this->authnet->error()) $this->models->get("orders")->error($this->authnet->response->messages->message->text, 'global');
		else {
			if ($type == "ARBCreateSubscriptionRequest") {
				$record["subscription_id"] = $this->authnet->response->subscriptionId;
				$record["expiration_date"] = date("Y-m-d H:i:s", strtotime($subscription["start_date"] . "+ " . $subscription["interval"] . $subscription["unit"]));
			} else if ($type == "ARBUpdateSubscriptionRequest") {
				$record["subscription_id"] = $subscription["subscriptionId"];
			}
		}
		foreach (array('amount', 'start_date', 'unit', 'trial_amount') as $field) {
			if (!empty($subscription[$field])) $record[$field] = $subscription[$field];
		}
		foreach (array('length' => 'interval', 'total_occurrences' => 'occurrences', 'trial_occurrences' => 'trials', 'expiration_date' => 'card_expiration') as $source => $dest) {
			if (!empty($subscription[$dest])) $record[$dest] = $subscription[$dest];
			else if (!empty($subscription[$source])) $record[$dest] = $subscription[$source];
		}
		if (!empty($subscription["card_number"])) {
			$record["card"] = substr($subscription["card_number"], -4);
		}
		$record["response"] = $this->authnet->response->asXML();
		$this->models->get("subscriptions")->store($record);
	}
	public function create($order, $subscription) {
		$subscription = $this->format($subscription);
		// check for required fields
		foreach (array('card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date', 'amount', 'total_occurrences', 'unit', 'length') as $field) {
			if (empty($subscription[$field])) $this->models->get("orders")->error('This field is required', $field);
		}
		if (isset($subscription['trial_occurrences']) && !isset($subscription['trial_amount'])) $this->models->get("orders")->error("This field is required when trial occurrences is specified", "trial_amount");
		if (empty($subscription["start_date"])) $subscription["start_date"] = date("Y-m-d");

		//if we have all the fields, continue processing
		if (!$this->models->get("orders")->errors()) {
			//parse card holder
			$cardholder = explode(" ", trim($subscription['card_holder']));
			$subscription['first_name'] = reset($cardholder);
			$subscription['last_name'] = end($cardholder);

			//address2
			if (!empty($subscription['address2'])) $subscription['address'] .= " ".$subscription['address2'];

			//strip dashes from card number
			$subscription['card_number'] = str_replace("-", "", $subscription['card_number']);

			//build expiration field
			if ((int) $subscription['expiration_date']['month'] < 10) $subscription['expiration_date']['month'] = '0'.$subscription['expiration_date']['month'];
			$subscription['expiration_date'] = $subscription['expiration_date']['year'].'-'.$subscription['expiration_date']['month'];

			//card code
			if (isset($subscription['cvv2'])) $subscription['card_code'] = $subscription['cvv2'];

			$this->authnet->ARBCreateSubscriptionRequest($subscription);
			$this->save("ARBCreateSubscriptionRequest", $order, $subscription);
		}
	}
	public function update($order, $subscription) {
		$subscription = $this->format($subscription);
		// check for required fields
		foreach (array('subscriptionId') as $field) {
			if (empty($subscription[$field])) $this->models->get("orders")->error('This field is required', $field);
		}

		//if we have all the fields, continue processing
		if (!errors()) {
			//parse card holder
			if (!empty($subscription['card_holder'])) {
				$cardholder = explode(" ", trim($subscription['card_holder']));
				$subscription['first_name'] = reset($cardholder);
				$subscription['last_name'] = end($cardholder);
			}

			//address2
			if (!empty($subscription['address']) && !empty($subscription['address2'])) $subscription['address'] .= " ".$subscription['address2'];

			//strip dashes from card number
			if (!empty($subscription['card_number'])) $subscription['card_number'] = str_replace("-", "", $subscription['card_number']);

			//build expiration field
			if (!empty($subscription['expiration_date'])) {
				if ((int) $subscription['expiration_date']['month'] < 10) $subscription['expiration_date']['month'] = '0'.$subscription['expiration_date']['month'];
				$subscription['expiration_date'] = $subscription['expiration_date']['year'].'-'.$subscription['expiration_date']['month'];
			}

			//card code
			if (isset($subscription['cvv2'])) $subscription['card_code'] = $subscription['cvv2'];

			$this->authnet->ARBUpdateSubscriptionRequest($subscription);
			$this->save("ARBUpdateSubscriptionRequest", $order, $subscription);
		}
	}
	public function cancel($order, $subscription) {
		foreach (array('subscriptionId') as $field) {
			if (empty($subscription[$field])) $this->models->get("orders")->error('This field is required', $field);
		}
		if (!$this->models->get("orders")->errors()) {
			$this->authnet->ARBCancelSubscriptionRequest($subscription);
			$this->save("ARBCancelSubscriptionRequest", $order, $subscription);
		}
	}
	public function status($id) {
		$this->authnet->ARBGetSubscriptionStatusRequest(["subscriptionId" => $id]);
		return $this->authnet->response->status;
	}
}
