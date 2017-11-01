<?php
namespace Starbug\Payment;

use Starbug\Core\ModelFactoryInterface;
use Omnipay\Common\GatewayInterface as OmnipayInterface;
use Omnipay\Common\CreditCard;

class TokenGateway extends Gateway implements TokenGatewayInterface {
  public function createSubscription($subscription) {
    if (!empty($subscription["product"])) {
      $product = $this->models->get("products")->load($subscription["product"]);
      $subscription["unit"] = $product["unit"];
      $subscription["interval"] = $product["interval"];
      $subscription["limit"] = $product["limit"];
    }
    if (empty($subscription["card"])) {
      $card = $this->createCard($subscription);
      $subscription["card"] = $card["id"];
    }
    foreach (array('amount', 'unit', 'interval') as $field) {
      if (empty($subscription[$field])) $this->models->get("orders")->error('This field is required', $field);
    }
    if (empty($subscription["start_date"])) $subscription["start_date"] = date("Y-m-d");
    if (!$this->models->get("orders")->errors()) {
      //prevent attempts to update subscriptions using this method
      unset($subscription["id"]);
      $next_billing = strtotime($subscription["start_date"] . "+ " . $subscription["interval"] . " " . $subscription["unit"]);
      $subscription["expiration_date"] = date("Y-m-d 00:00:00", $next_billing + 86400);
      $this->saveSubscription($subscription);
      if (!$this->models->get("subscriptions")->errors()) {
        //create a bill for the next payment
        $this->models->get("bills")->store([
          "amount" => $subscription["amount"],
          "due_date" => $subscription["expiration_date"],
          "scheduled_date" => date("Y-m-d 00:00:00", $next_billing),
          "subscriptions_id" => $this->models->get("subscriptions")->insert_id,
          "scheduled" => "1"
        ]);
      }
    }
  }
  public function updateSubscription($subscription) {
    if (empty($subscription["id"])) $this->models->get("subscriptions")->error('You must specify a subscription to update', 'global');
    else $this->saveSubscription(["id" => $subscription["id"], "card" => $subscription["card"]]);
  }
  public function cancelSubscription($subscription) {
    if (empty($subscription["id"])) $this->models->get("subscriptions")->error('You must specify a subscription to update', 'global');
    else $this->saveSubscription(["id" => $subscription["id"], "canceled" => 1, "active" => "0"]);
  }
  public function processSubscription($subscription) {
    $complete = false;
    //we will assume the subscription is neither canceled, completed, or up to date
    $purchase = [
      "card" => $subscription["card"],
      "orders_id" => $subscription["orders_id"],
      "subscriptions_id" => $subscription["id"],
      "amount" => $subscription["amount"]
    ];
    $this->purchase($purchase);
    if (!$this->models->get("orders")->errors()) {
      $update = [
        "id" => $subscription["id"],
        "expiration_date" => date("Y-m-d H:i:s", strtotime($subscription["expiration_date"] . "+ " . $subscription["interval"] . " " . $subscription["unit"]))
      ];
      if (!empty($subscription["limit"]) && $subscription["payments"] == $subscription["limit"]) {
        $complete = true;
        $update["completed"] = 1;
        $update["active"] = "0";
      }
      $this->models->get("subscriptions")->store($update);
      //the payment succeeded so add it to the bill and mark it as paid
      $payment = $this->models->get("payments")->insert_id;
      $this->models->get("bills")->store(["id" => $subscription["bill"], "payments" => "+".$payment, "paid" => "1"]);
      if (!$complete && $subscription["active"] && !$subscription["canceled"]) {
        //create a bill for the next payment
        $this->models->get("bills")->store([
          "amount" => $subscription["amount"],
          "due_date" => $subscription["expiration_date"],
          "scheduled_date" => date("Y-m-d 00:00:00", strtotime($subscription["expiration_date"]) - 86400),
          "subscriptions_id" => $subscription["id"],
          "scheduled" => "1"
        ]);
      }
    } else {
      if (isset($this->models->get("payments")->insert_id)) {
        //the payment was decline so add it to the bill and unschedule it
        $payment = $this->models->get("payments")->insert_id;
        $this->models->get("bills")->store(["id" => $subscription["bill"], "payments" => "+".$payment, "scheduled" => "0"]);
      }
      $this->sendDeclinedNotification($subscription["id"]);
    }
  }
  protected function saveSubscription($subscription) {
    $record = [];
    foreach (array('id', 'orders_id', 'product', 'amount', 'start_date', 'unit', 'interval', 'limit', 'card', 'canceled', 'completed', 'expiration_date') as $field) {
      if (!empty($subscription[$field])) $record[$field] = $subscription[$field];
    }
    $this->models->get("subscriptions")->store($record);
    if (!$this->models->get("subscriptions")->errors()) {
      if (!empty($subscription["payment"])) {
        $id = empty($subscription["id"]) ? $this->models->get("subscriptions")->insert_id : $subscription["id"];
        $this->models->get("payments")->store(["id" => $subscription["payment"], "subscriptions_id" => $id]);
      }
    }
  }
  public function createCard($options) {
    $options = $this->validateCard($options);
    //if we have all the fields, continue processing
    if (!$this->models->get("orders")->errors()) {
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
        $this->models->get("payment_cards")->store($record);
        return $this->getCard();
      } else {
        $this->models->get("orders")->error($response->getMessage(), 'global');
      }
    }
  }
  public function getCard($id = false) {
    if (!$id) $id = $this->models->get("payment_cards")->insert_id;
    return $this->models->get("payment_cards")->load($id);
  }
  public function purchase($payment) {
    if (!empty($payment["card"])) {
      $card = $this->getCard($payment["card"]);
      $payment["cardReference"] = json_encode(["customerPaymentProfileId" => $card["card_reference"], "customerProfileId" => $card["customer_reference"]]);
    }
    // check for required fields
    foreach (array('cardReference', 'amount') as $field) {
      if (empty($payment[$field])) $this->models->get("orders")->error('This field is required', $field);
    }
    //if we have all the fields, continue processing
    if (!$this->models->get("orders")->errors()) {
      $response = $this->gateway->purchase(["amount" => floatval($payment["amount"]/100), "cardReference" => $payment["cardReference"]])->send();
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
  protected function sendDeclinedNotification($sid) {
    $subscription = $this->models->get("subscriptions")->query()->condition("subscriptions.id", $sid)
      ->select("subscriptions.product.name as description,subscriptions.orders_id.email as email")
      ->select(["brand", "number", "month", "year"], "subscriptions.card")->one();
    $bill = $this->models->get("bills")->query()->condition("subscriptions_id", $sid)->sort("due_date DESC")->one();
    $reason = $this->models->get("orders")->errors("global", true);
    $subscription["details"] = implode("\n", [
      "<p><strong>Card:</strong>".$subscription["brand"]." xxxx".$subscription["number"]."</p>",
      "<p><strong>Payment amount:</strong>".$this->priceFormatter->format($subscription["amount"])."</p>",
      "<p><strong>Message:</strong> ".reset($reason)."</p>"
    ]);
    $data = [
      "user" => $this->user->getUser(),
      "payment" => $subscription,
      "bill" => $bill
    ];
    $this->mailer->send(["template" => "Payment Declined", "to" => $subscription["email"]], $data);
  }
}
