<?php
namespace Starbug\Payments;

use Omnipay\Common\CreditCard;

class TokenGateway extends Gateway implements TokenGatewayInterface {
  public function createCard($options) {
    $options = $this->validateCard($options);
    // if we have all the fields, continue processing
    if (!$this->db->errors("orders")) {
      $card = new CreditCard($options);
      $response = $this->gateway->createCard(["card" => $card, "forceCardUpdate" => true] + $options)->send();
      if ($response->isSuccessful()) {
        $record = [
          "customer_reference" => $response->getCustomerProfileId(),
          "card_reference" => $response->getCustomerPaymentProfileId(),
          "brand" => $card->getBrand(),
          "number" => $card->getNumberLastFour(),
          "month" => $card->getExpiryMonth(),
          "year" => $card->getExpiryYear()
        ];
        $this->db->store("payment_cards", $record);
        return $this->getCard();
      } else {
        $this->db->error($response->getMessage(), "global", "orders");
      }
    }
  }
  public function getCard($id = false) {
    if (!$id) {
      $id = $this->db->getInsertId("payment_cards");
    }
    return $this->db->get("payment_cards", $id);
  }
  public function purchase($payment) {
    if (!empty($payment["card"])) {
      $card = $this->getCard($payment["card"]);
      $payment["cardReference"] = json_encode(["customerPaymentProfileId" => $card["card_reference"], "customerProfileId" => $card["customer_reference"]]);
    }
    // check for required fields
    foreach (['cardReference', 'amount'] as $field) {
      if (empty($payment[$field])) {
        $this->db->error("This field is required", $field, "orders");
      }
    }
    // if we have all the fields, continue processing
    if (!$this->db->errors("orders")) {
      $response = $this->gateway->purchase(["amount" => floatval($payment["amount"]/100), "cardReference" => $payment["cardReference"]])->send();
      if (!$response->isSuccessful()) {
        $this->db->error($response->getMessage(), "global", "orders");
      }
      $code = $response->getResultCode();
      $txn_id = "";
      if ($txn = $response->getTransactionReference(false)) {
        $txn_id = $txn->getTransId();
      }
      $record = ["orders_id" => $payment["orders_id"], "subscriptions_id" => $payment["subscriptions_id"], "response_code" => $code, "txn_id" => $txn_id, "amount" => $payment["amount"], "response" => $response->getData()->asXML()];
      $this->db->store("payments", $record);
    }
  }
}
