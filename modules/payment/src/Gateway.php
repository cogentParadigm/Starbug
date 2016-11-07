<?php
namespace Starbug\Payment;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\MailerInterface;
use Starbug\Core\IdentityInterface;
use Omnipay\Common\GatewayInterface as OmnipayInterface;
use Omnipay\Common\CreditCard;
class Gateway implements GatewayInterface {
	public function __construct(ModelFactoryInterface $models, OmnipayInterface $gateway, MailerInterface $mailer, IdentityInterface $user) {
		$this->models = $models;
		$this->gateway = $gateway;
		$this->mailer = $mailer;
		$this->user = $user;
	}
	public function getName() {
		return $this->gateway->getName();
	}
	public function getShortName() {
		return $this->gateway->getShortName();
	}
	public function getDefaultParameters() {
		return $this->gateway->getDefaultParameters();
	}
	public function initialize(array $parameters = []) {
		return $this->gateway->initialize();
	}
	public function getParameters() {
		return $this->gateway->getParameters();
	}
	public function purchase($payment) {
		$payment = $this->validateCard($payment, ["amount"]);
		//if we have all the fields, continue processing
		if (!$this->models->get("orders")->errors()) {
			$card = new CreditCard($payment);
			$response = $this->gateway->purchase(["amount" => floatval($payment["amount"]/100), "card" => $card])->send();
			if (!$response->isSuccessful()) {
				$this->models->get("orders")->error($response->getMessage(), 'global');
			}
			$code = $response->getResultCode();
			$txn_id = "";
			if ($txn = $response->getTransactionReference(false)) {
				$txn_id = $txn->getTransId();
			}
			$record = ["orders_id" => $payment["orders_id"], "subscriptions_id" => $payment["subscriptions_id"], "response_code" => $code, "txn_id" => $txn_id, "amount" => $payment["amount"], "response" => $response->getData()->asXML()];
			$this->models->get("payments")->store($record);
		}
	}
	public function __call($method, $args) {
		return call_user_func_array([$this->gateway, $method], $args);
	}
	protected function validateCard($payment, $plus = array()) {
		$fields = array_merge(
			array('card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date'),
			$plus
		);
		foreach ($fields as $field) {
			if (empty($payment[$field])) $this->models->get("orders")->error('This field is required', $field);
		}
		//parse card holder
		$cardholder = explode(" ", trim($payment['card_holder']));
		$payment['lastName'] = array_pop($cardholder);
		$payment['firstName'] = implode(" ", $cardholder);
		//address
		$payment['address1'] = $payment['address'];
		//strip dashes from card number
		$payment['number'] = str_replace("-", "", $payment['card_number']);
		//build expiration field
		if ((int) $payment['expiration_date']['month'] < 10) $payment['expiration_date']['month'] = '0'.$payment['expiration_date']['month'];
		$payment['expiryYear'] = $payment['expiration_date']['year'];
		$payment['expiryMonth'] = $payment['expiration_date']['month'];
		//postCode
		$payment['postCode'] = $payment['zip'];
		return $payment;
	}
}
?>
