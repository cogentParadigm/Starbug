<?php
namespace Starbug\Payment;

use Omnipay\Common\CreditCard;

class TokenGateway extends Gateway implements TokenGatewayInterface {
  public function createSubscription($subscription) {
    if (!empty($subscription["product"])) {
      $product = $this->db->get("products", $subscription["product"]);
      $subscription["unit"] = $product["unit"];
      $subscription["interval"] = $product["interval"];
      $subscription["limit"] = $product["limit"];
    }
    if (empty($subscription["card"])) {
      $card = $this->createCard($subscription);
      $subscription["card"] = $card["id"];
    }
    foreach (['amount', 'unit', 'interval'] as $field) {
      if (empty($subscription[$field])) $this->db->error("This field is required", $field, "orders");
    }
    if (empty($subscription["start_date"])) $subscription["start_date"] = date("Y-m-d");
    if (!$this->db->errors("orders")) {
      // prevent attempts to update subscriptions using this method
      unset($subscription["id"]);
      $next_billing = strtotime($subscription["start_date"] . "+ " . $subscription["interval"] . " " . $subscription["unit"]);
      $subscription["expiration_date"] = date("Y-m-d 00:00:00", $next_billing + 86400);
      $this->saveSubscription($subscription);
      if (!$this->db->errors("subscriptions")) {
        // create a bill for the next payment
        $this->db->store("bills", [
          "amount" => $subscription["amount"],
          "due_date" => $subscription["expiration_date"],
          "scheduled_date" => date("Y-m-d 00:00:00", $next_billing),
          "subscriptions_id" => $this->db->getInsertId("subscriptions"),
          "scheduled" => "1"
        ]);
      }
    }
  }
  public function updateSubscription($subscription) {
    if (empty($subscription["id"])) {
      $this->db->error("You must specify a subscription to update", "global", "subscriptions");
    } else {
      $this->saveSubscription(["id" => $subscription["id"], "card" => $subscription["card"]]);
    }
  }
  public function cancelSubscription($subscription) {
    if (empty($subscription["id"])) {
      $this->db->error("You must specify a subscription to update", "global", "subscriptions");
    } else {
      $this->saveSubscription(["id" => $subscription["id"], "canceled" => 1, "active" => "0"]);
    }
  }
  public function processSubscription($subscription) {
    $complete = false;
    // we will assume the subscription is neither canceled, completed, or up to date
    $purchase = [
      "card" => $subscription["card"],
      "orders_id" => $subscription["orders_id"],
      "subscriptions_id" => $subscription["id"],
      "amount" => $subscription["amount"]
    ];
    $this->purchase($purchase);
    if (!$this->db->errors("orders")) {
      $update = [
        "id" => $subscription["id"],
        "expiration_date" => date("Y-m-d H:i:s", strtotime($subscription["expiration_date"] . "+ " . $subscription["interval"] . " " . $subscription["unit"]))
      ];
      if (!empty($subscription["limit"]) && $subscription["payments"] == $subscription["limit"]) {
        $complete = true;
        $update["completed"] = 1;
        $update["active"] = "0";
      }
      $this->db->store("subscriptions", $update);
      // the payment succeeded so add it to the bill and mark it as paid
      $payment = $this->db->getInsertId("payments");
      $this->db->store("bills", ["id" => $subscription["bill"], "payments" => "+".$payment, "paid" => "1"]);
      if (!$complete && $subscription["active"] && !$subscription["canceled"]) {
        // create a bill for the next payment
        $this->db->store("bills", [
          "amount" => $subscription["amount"],
          "due_date" => $subscription["expiration_date"],
          "scheduled_date" => date("Y-m-d 00:00:00", strtotime($subscription["expiration_date"]) - 86400),
          "subscriptions_id" => $subscription["id"],
          "scheduled" => "1"
        ]);
      }
    } else {
      if (!is_null($this->db->getInsertId("payments"))) {
        // the payment was decline so add it to the bill and unschedule it
        $payment = $this->db->getInsertId("payments");
        $this->db->store("bills", ["id" => $subscription["bill"], "payments" => "+".$payment, "scheduled" => "0"]);
      }
      $this->sendDeclinedNotification($subscription["id"]);
    }
  }
  protected function saveSubscription($subscription) {
    $record = [];
    foreach (['id', 'orders_id', 'product', 'amount', 'start_date', 'unit', 'interval', 'limit', 'card', 'canceled', 'completed', 'expiration_date'] as $field) {
      if (!empty($subscription[$field])) {
        $record[$field] = $subscription[$field];
      }
    }
    $this->db->store("subscriptions", $record);
    if (!$this->db->errors("subscriptions")) {
      if (!empty($subscription["payment"])) {
        $id = empty($subscription["id"]) ? $this->db->getInsertId("subscriptions") : $subscription["id"];
        $this->db->store("payments", ["id" => $subscription["payment"], "subscriptions_id" => $id]);
      }
    }
  }
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
  protected function sendDeclinedNotification($sid) {
    $subscription = $this->db->query("subscriptions")->condition("subscriptions.id", $sid)
      ->select("subscriptions.*")
      ->select("subscriptions.product.name as description,subscriptions.orders_id.email as email")
      ->select(["brand", "number", "month", "year"], "subscriptions.card")->one();
    $bill = $this->db->query("bills")->condition("subscriptions_id", $sid)->sort("due_date DESC")->one();
    $reason = $this->db->errors("orders.global", true);
    $subscription["details"] = implode("\n", [
      "<p><strong>Card:</strong>".$subscription["brand"]." xxxx".$subscription["number"]."</p>",
      "<p><strong>Payment amount:</strong>".$this->priceFormatter->format($subscription["amount"])."</p>",
      "<p><strong>Message:</strong> ".reset($reason)."</p>"
    ]);
    $data = [
      "user" => $this->session->getData(),
      "payment" => $subscription,
      "bill" => $bill
    ];
    $this->mailer->send(["template" => "Payment Declined", "to" => $subscription["email"]], $data);
  }
}
