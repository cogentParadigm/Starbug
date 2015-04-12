<?php
include("classes/Authnet.php");
/**
 * check if a gateway is in test mode
 * @param int/string $gateway the name or id of the gateway
 * @return true if the gateway is in test mode
 */
function is_test_mode($gateway) {
	$field_name = (is_numeric($gateway)) ? "id" : "name";
	$gateway = query("payment_gateways", "select:is_test_mode  where:$field_name=?  limit:1", array($gateway));
	if ($gateway['is_test_mode']) return true;
	return false;
}
/**
 * get payment setting
 * @param int/string $gateway either the id or the name of the gateway
 * @param string $setting the setting name. eg. 'Login ID'
 * @return string the setting value - will determine whether to send you live or test mode value based on gateway settings
 */
function payment_settings($gateway, $setting) {
	$field_name = (is_numeric($gateway)) ? "id" : "name";
	return reset(query("payment_gateway_settings,payment_gateways", "select:IF(payment_gateways.is_test_mode=1, payment_gateway_settings.test_mode_value, payment_gateway_settings.live_mode_value) as value  where:payment_gateways.$field_name=? && payment_gateway_settings.name=?  limit:1", array($gateway, $setting)));
}

/**
 * make a call to createTransactionRequest - initiates an AIM (Advanced Integration Method) Transaction
 * @param array $fields payment fields - type, amount, card_holder, card_number, expiration_date, card_code, address, city, state, zip
 *						possible values for 'type' are:
 *						- authOnlyTransaction
 *						- authCaptureTransaction (default)
 *						- captureOnlyTransaction
 *						- refundTransaction
 *						- priorAuthCaptureTransaction
 *						- voidTransaction
 * @return AIM response object if successful, false otherwise. errors will be set on failures
 */
function AIMTransaction($fields) {
	// check for required fields
	foreach (array('card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date', 'amount') as $field) {
		if (empty($fields[$field])) error('This field is required', $field);
	}

	//if we have all the fields, continue processing
	if (!errors()) {
		//parse card holder
		$cardholder = explode(" ", trim($fields['card_holder']));
		$fields['first_name'] = reset($cardholder);
		$fields['last_name'] = end($cardholder);

		//set transaction type
		if (empty($fields['type'])) $fields['type'] = "authCaptureTransaction";

		//address2
		if (!empty($fields['address2'])) $fields['address'] .= " ".$fields['address2'];

		//strip dashes from card number
		$fields['card_number'] = str_replace("-", "", $fields['card_number']);

		//build expiration field
		if ((int) $fields['expiration_date']['month'] < 10) $fields['expiration_date']['month'] = '0'.$fields['expiration_date']['month'];
		$fields['expiration_date'] = $fields['expiration_date']['year'].'-'.$fields['expiration_date']['month'];

		//card code
		if (isset($fields['cvv2'])) $fields['card_code'] = $fields['cvv2'];

		$authnet = new Authnet();
		$authnet->AIMCreateTransactionRequest($fields);
		if ($authnet->error()) error($authnet->text, 'global');
		return $authnet;

	}
}


/**
 * make a call to ARBCreateSubscriptionRequest to initiate an ARB (Automated Recurring Billing) Subscription
 * @param array $fields payment fields - card_holder, address, city, state, zip, card_number, expiration_date, unit, length, start_date, total_occurrences
 * @return ARB response object if successful, false otherwise. errors will be set on failures
 */
function ARBCreateSubscriptionRequest($fields) {
	// check for required fields
	foreach (array('card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date', 'amount', 'total_occurrences', 'unit', 'length', 'start_date') as $field) {
		if (empty($fields[$field])) error('This field is required', $field);
	}
	if (isset($fields['trial_occurrences']) && !isset($fields['trial_amount'])) error("This field is required when trial occurrences is specified", "trial_amount");

	//if we have all the fields, continue processing
	if (!errors()) {
		//parse card holder
		$cardholder = explode(" ", trim($fields['card_holder']));
		$fields['first_name'] = reset($cardholder);
		$fields['last_name'] = end($cardholder);

		//address2
		if (!empty($fields['address2'])) $fields['address'] .= " ".$fields['address2'];

		//strip dashes from card number
		$fields['card_number'] = str_replace("-", "", $fields['card_number']);

		//build expiration field
		if ((int) $fields['expiration_date']['month'] < 10) $fields['expiration_date']['month'] = '0'.$fields['expiration_date']['month'];
		$fields['expiration_date'] = $fields['expiration_date']['year'].'-'.$fields['expiration_date']['month'];

		//card code
		if (isset($fields['cvv2'])) $fields['card_code'] = $fields['cvv2'];

		$authnet = new Authnet();
		$authnet->ARBCreateSubscriptionRequest($fields);
		if ($authnet->error()) error($authnet->text, 'global');
		//if ($authnet->error()) error('There was an issue processing your card. Please check your information and submit the card again.', 'global');
		return $authnet;

	}

}

/**
 * make a call to ARBUpdateSubscriptionRequest to update an ARB (Automated Recurring Billing) Subscription
 * @param array $fields payment fields - card_holder, address, city, state, zip, card_number, expiration_date, unit, length, start_date, total_occurrences
 * @return ARB subscription ID if successful, false otherwise. errors will be set on failures
 */
function ARBUpdateSubscriptionRequest($fields) {

	// check for required fields
	foreach (array('subscriptionId') as $field) {
		if (empty($fields[$field])) error('This field is required', $field);
	}

	//if we have all the fields, continue processing
	if (!errors()) {
		//parse card holder
		if (!empty($fields['card_holder'])) {
			$cardholder = explode(" ", trim($fields['card_holder']));
			$fields['first_name'] = reset($cardholder);
			$fields['last_name'] = end($cardholder);
		}

		//address2
		if (!empty($fields['address']) && !empty($fields['address2'])) $fields['address'] .= " ".$fields['address2'];

		//strip dashes from card number
		if (!empty($fields['card_number'])) $fields['card_number'] = str_replace("-", "", $fields['card_number']);

		//build expiration field
		if (!empty($fields['expiration_date'])) {
			if ((int) $fields['expiration_date']['month'] < 10) $fields['expiration_date']['month'] = '0'.$fields['expiration_date']['month'];
			$fields['expiration_date'] = $fields['expiration_date']['year'].'-'.$fields['expiration_date']['month'];
		}

		//card code
		if (isset($fields['cvv2'])) $fields['card_code'] = $fields['cvv2'];

		$authnet = new Authnet();
		$authnet->ARBUpdateSubscriptionRequest($fields);
		if ($authnet->error()) error($authnet->text, 'global');
		return $authnet;

	}

}

/**
 * make a call to ARBCancelSubscriptionRequest to cancel an ARB (Automated Recurring Billing) Subscription
 * @param array $fields - subscriptionId, refId
 * @return bool success
 */
function ARBCancelSubscriptionRequest($fields) {

	// check for required fields
	foreach (array('subscriptionId') as $field) {
		if (empty($fields[$field])) error('This field is required', $field);
	}

	//if we have all the fields, continue processing
	if (!errors()) {
		$authnet = new Authnet();
		$authnet->ARBCancelSubscriptionRequest($fields);
		if ($authnet->error()) error($authnet->text, 'global');
		return $authnet;
	}

}

/**
 * make a call to ARBGetSubscriptionStatusRequest to get an ARB (Automated Recurring Billing) Subscription
 * @param array $fields - subscriptionId, refId
 * @return ARB response object
 */
function ARBGetSubscriptionStatusRequest($fields) {

	// check for required fields
	foreach (array('subscriptionId') as $field) {
		if (empty($fields[$field])) error('This field is required', $field);
	}

	//if we have all the fields, continue processing
	if (!errors()) {

		$authnet = new Authnet();
		$authnet->ARBGetSubscriptionStatusRequest($fields);
		if ($authnet->error()) error($authnet->text, 'global');
		return $authnet;

	}
}
?>
