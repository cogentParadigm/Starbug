<?php
namespace Starbug\Payment;
class PaymentSubscription implements PaymentInterface {
	public function __construct(ModelFactoryInterface $models, Authnet $authnet) {
		$this->storage = $models->get("subscriptions");
		$this->authnet = $authnet;
	}
	public function create($order, $subscription) {
		// check for required fields
		foreach (array('card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date', 'amount', 'total_occurrences', 'unit', 'length', 'start_date') as $field) {
			if (empty($subscription[$field])) $this->storage->error('This field is required', $field);
		}
		if (isset($subscription['trial_occurrences']) && !isset($subscription['trial_amount'])) $this->storage->error("This field is required when trial occurrences is specified", "trial_amount");

		//if we have all the fields, continue processing
		if (!$this->storage->errors()) {
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
			if ($authnet->error()) $this->storage->error($authnet->response->messages->message->text, 'global');
			$subscription_id = $this->authnet->response->subscriptionId;
			$record = [
				"orders_id" => $order["id"],
				"subscription_id" => $subscription_id,
				"amount" => $subscription["amount"],
				"start_date" => $subscription["start_date"],
				"interval" => $subscription["length"],
				"unit" => $subscription["unit"],
				"occurrences" => $subscription["total_occurrences"],
				"card" => substr($subscription["card_number"], -4),
				"card_expiration" => $subscription["expiration_date"],
				"response" => $authnet->response->asXML()
			];
			if (!empty($subscription["trial_occurrences"])) {
				$record += [
					"trials" => $subscription["trial_occurrences"],
					"trial_amount" => $subscription["trial_amount"]
				];
			}
			$this->storage->store($record);
		}
	}
}
?>
