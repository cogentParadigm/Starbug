<?php
namespace Starbug\Payment;

use Omnipay\Common\GatewayInterface as OmnipayInterface;
use Omnipay\Common\CreditCard;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\MailerInterface;

class Gateway implements GatewayInterface {
  public function __construct(DatabaseInterface $db, OmnipayInterface $gateway, MailerInterface $mailer, SessionHandlerInterface $session, PriceFormatterInterface $priceFormatter) {
    $this->db = $db;
    $this->gateway = $gateway;
    $this->mailer = $mailer;
    $this->session = $session;
    $this->priceFormatter = $priceFormatter;
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
    // if we have all the fields, continue processing
    if (!$this->db->errors("orders")) {
      $card = new CreditCard($payment);
      $response = $this->gateway->purchase(["amount" => floatval($payment["amount"]/100), "card" => $card])->send();
      if (!$response->isSuccessful()) {
        $this->db->error($response->getMessage(), "global", "orders");
      }
      $code = $response->getResultCode();
      $txn_id = "";
      if ($txn = $response->getTransactionReference(false)) {
        $txn_id = $txn->getTransId();
      }
      $record = ["orders_id" => $payment["orders_id"], "response_code" => $code, "txn_id" => $txn_id, "amount" => $payment["amount"], "response" => json_encode($response->getData())];
      if (isset($payment["subscriptions_id"])) {
        $record["subscriptions_id"] = $payment["subscriptions_id"];
      }
      $this->db->store("payments", $record);
    }
  }
  public function __call($method, $args) {
    return call_user_func_array([$this->gateway, $method], $args);
  }
  protected function validateCard($payment, $plus = []) {
    $fields = array_merge(
      ['card_holder', 'address', 'city', 'state', 'zip', 'card_number', 'expiration_date'],
      $plus
    );
    foreach ($fields as $field) {
      if (empty($payment[$field])) {
        $this->db->error("This field is required", $field, "orders");
      }
    }
    // parse card holder
    $cardholder = explode(" ", trim($payment['card_holder']));
    $payment['lastName'] = array_pop($cardholder);
    $payment['firstName'] = implode(" ", $cardholder);
    // address
    $payment['address1'] = $payment['address'];
    // strip dashes from card number
    $payment['number'] = str_replace("-", "", $payment['card_number']);
    // build expiration field
    if (is_array($payment["expiration_date"])) {
      if ((int) $payment['expiration_date']['month'] < 10) {
        $payment['expiration_date']['month'] = '0'.$payment['expiration_date']['month'];
      }
      $payment['expiryYear'] = $payment['expiration_date']['year'];
      $payment['expiryMonth'] = $payment['expiration_date']['month'];
    } else {
      $expiration = explode("/", $payment["expiration_date"]);
      $payment['expiryYear'] = $expiration[1];
      $payment['expiryMonth'] = $expiration[0];
    }
    // postCode
    $payment['postCode'] = $payment['zip'];
    return $payment;
  }
}
