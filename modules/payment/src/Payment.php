<?php
namespace Starbug\Payment;
class Payment implements PaymentInterface {
	public function __construct(ModelFactoryInterface $models, Authnet $authnet) {
		$this->storage = $models->get("payments");
		$this->authnet = $authnet;
	}
	public function create($order, $payment) {
		// check for required fields
		foreach (array('card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date', 'amount') as $field) {
			if (empty($payment[$field])) $this->storage->error('This field is required', $field);
		}
		//if we have all the fields, continue processing
		if (!$this->storage->errors()) {
			//parse card holder
			$cardholder = explode(" ", trim($payment['card_holder']));
			$payment['first_name'] = reset($cardholder);
			$payment['last_name'] = end($cardholder);

			//set transaction type
			if (empty($payment['type'])) $payment['type'] = "authCaptureTransaction";
			//address2
			if (!empty($payment['address2'])) $payment['address'] .= " ".$payment['address2'];
			//strip dashes from card number
			$payment['card_number'] = str_replace("-", "", $payment['card_number']);
			//build expiration field
			if ((int) $payment['expiration_date']['month'] < 10) $payment['expiration_date']['month'] = '0'.$payment['expiration_date']['month'];
			$payment['expiration_date'] = $payment['expiration_date']['year'].'-'.$payment['expiration_date']['month'];
			//card code
			if (isset($payment['cvv2'])) $payment['card_code'] = $payment['cvv2'];

			$this->authnet->AIMCreateTransactionRequest($payment);
			if ($this->authnet->error()) $this->storage->error($authnet->response->transactionResponse->errors->error->errorText, 'global');
			$code = $this->authnet->response->transactionResponse->responseCode;
			$txn_id = $this->authnet->response->transactionResponse->transId;
			$record = ["orders_id" => $order["id"], "response_code" => $code, "txn_id" => $txn_id, "amount" => $payment["amount"], "response" => $authnet->response->asXML()];
			$this->storage->store($record);
		}
	}
}
?>
