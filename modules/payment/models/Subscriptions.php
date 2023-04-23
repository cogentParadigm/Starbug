<?php
namespace Starbug\Payment;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Emails\MailerInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class Subscriptions extends Table {

  public function __construct(DatabaseInterface $db, SchemerInterface $schemer, SessionHandlerInterface $session, TokenGatewayInterface $gateway, CollectionFactoryInterface $collections, PriceFormatterInterface $priceFormatter, MailerInterface $mailer) {
    parent::__construct($db, $schemer);
    $this->session = $session;
    $this->gateway = $gateway;
    $this->collections = $collections;
    $this->priceFormatter = $priceFormatter;
    $this->mailer = $mailer;
  }

  public function update($subscription, $email = true) {
    if (empty($subscription["card"])) {
      // populate the billing address
      $address = $this->query()->condition("subscriptions.id", $subscription["id"])
        ->select("subscriptions.orders_id.email as email")
        ->select(["*", "country.name as country"], "subscriptions.orders_id.billing_address")->one();
      $subscription['email'] = $address['email'];
      $subscription['country'] = $address['country'];
      $subscription['address'] = $address['address1'];
      $subscription['address2'] = $address['address2'];
      $subscription['zip'] = $address['postal_code'];
      $subscription['city'] = $address['locality'];
      $subscription['state'] = $address['administrative_area'];
      if (is_numeric($subscription['state'])) {
        $state = $this->query("provinces")->condition("id", $subscription['state'])->one();
        $subscription['state'] = $state['name'];
      }
      $card = $this->gateway->createCard($subscription);
      $subscription["card"] = $card["id"];
    }
    if (!$this->errors()) {
      $this->gateway->updateSubscription($subscription);
      if ($email && !$this->db->errors()) {
        $subscription = $this->query()->condition("subscriptions.id", $subscription["id"])
          ->select("subscriptions.product.name as description,subscriptions.orders_id.email as email")
          ->select(["brand", "number", "month", "year"], "subscriptions.card")->one();
        $bill = $this->query("bills")->condition("subscriptions_id", $subscription["id"])->sort("due_date DESC")->one();
        $subscription["details"] = implode("\n", [
          "<p><strong>Card:</strong>".$subscription["brand"]." xxxx".$subscription["number"]."</p>",
          "<p><strong>Next payment date:</strong> ".date("l, F j", strtotime($bill["scheduled_date"]))."</p>"
        ]);
        $data = [
          "user" => $this->session->getData(),
          "subscription" => $subscription
        ];
        $this->mailer->send(["template" => "Update Subscription", "to" => $subscription["email"]], $data);
      }
    }
  }

  public function cancel($subscription) {
    $this->gateway->cancelSubscription($subscription);
  }

  public function payment($data) {
    $this->update($data, false);
    $subscription = $this->collections->get(ExpiredSubscriptionsCollection::class)->one(["id" => $data["bill"]]);
    $this->gateway->processSubscription($subscription);
    if (!$this->db->errors()) {
      $subscription = $this->query()->condition("subscriptions.id", $data["id"])
        ->select("subscriptions.product.name as description,subscriptions.orders_id.email as email")
        ->select(["brand", "number", "month", "year"], "subscriptions.card")->one();
      $bill = $this->query("bills")->condition("subscriptions_id", $data["id"])->sort("due_date DESC")->one();
      $subscription["details"] = implode("\n", [
        "<p><strong>Card:</strong>".$subscription["brand"]." xxxx".$subscription["number"]."</p>",
        "<p><strong>Payment amount:</strong>".$this->priceFormatter->format($subscription["amount"])."</p>",
        "<p><strong>Next payment date:</strong> ".date("l, F j", strtotime($bill["scheduled_date"]))."</p>"
      ]);
      $data = [
        "user" => $this->session->getData(),
        "payment" => $subscription
      ];
      $this->mailer->send(["template" => "Payment Confirmation", "to" => $subscription["email"]], $data);
    }
  }
}
