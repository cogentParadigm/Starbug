<?php
namespace Starbug\Payment\Cart;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;
use Starbug\Payment\Cart;
use Starbug\Payment\GatewayInterface;
use Starbug\Payment\TokenGatewayInterface;
use Starbug\Queue\QueueManagerInterface;

class Payment extends Save {
  protected $model = "orders";
  public function __construct(ModelFactoryInterface $models, Cart $cart, SessionHandlerInterface $session, GatewayInterface $gateway, TokenGatewayInterface $subscriptions, QueueManagerInterface $queues) {
    $this->models = $models;
    $this->cart = $cart;
    $this->session = $session;
    $this->gateway = $gateway;
    $this->subscriptions = $subscriptions;
    $this->queues = $queues;
  }
  public function handle(array $payment, BundleInterface $state): BundleInterface {
    if (empty($payment['id'])) {
      $order = $this->cart->getOrder();
    } else {
      $order = $this->load($payment['id']);
    }

    // $this->request->setPost('orders', 'id', $order['id']);

    // populate the billing address
    $billing_address = $order["billing_same"] ? $order["shipping_address"] : $order["billing_address"];
    $address = $this->query("address")->condition("address.id", $billing_address)->select("address.*,address.country.name as country")->one();
    $payment['country'] = $address['country'];
    $payment['address'] = $address['address1'];
    $payment['address2'] = $address['address2'];
    $payment['zip'] = $address['postal_code'];
    $payment['city'] = $address['locality'];
    $payment['state'] = $address['administrative_area'];
    if (is_numeric($payment['state'])) {
      $province = $this->query("provinces")->condition("id", $payment['state'])->one();
      $payment['state'] = $province['name'];
    }

    // prepare details to be added to the order
    $orderTotal = 0;
    $ammend = ["id" => $order['id'], "email" => $payment['email'], "phone" => $payment['phone']];
    if ($this->session->loggedIn()) {
      $ammend['owner'] = $this->session->getUserId();
    }

    $total = $this->getOrderTotal($order["id"]);
    if ($total) {
      $orderTotal += $total;
      $this->purchase($payment + ["amount" => $total, "orders_id" => $order["id"]]);
    }

    // determine recurring payment amounts
    $lines = $this->query("product_lines")
      ->condition("orders_id", $order['id'])
      ->condition("product_lines.product.payment_type", "recurring")->all();
    foreach ($lines as $line) {
      $price = $line["price"] * $line["qty"];
      $orderTotal += $price;
      $this->purchase($payment + ["amount" => $price, "orders_id" => $order["id"]]);
      if (!$this->errors()) {
        $this->subscriptions->createSubscription(["orders_id" => $order["id"], "amount" => $price, "product" => $line["product"], "payment" => $this->models->get("payments")->insert_id] + $payment);
      }
    }

    $ammend["total"] = $orderTotal;

    $this->store($ammend);
    $this->onPaymentCompleted($ammend + $order);
    return $this->getErrorState($state);
  }
  protected function purchase($payment) {
    if (empty($payment["card"])) {
      $this->gateway->purchase($payment);
    } else {
      $this->subscriptions->purchase($payment);
    }
  }

  protected function getOrderTotal($id) {
    // determine single payment amount
    // WARN: prices not validated, lines could be stale
    $lines = $this->query("product_lines")
      ->condition("orders_id", $id)
      ->condition("product_lines.product.payment_type", "single")
      ->select("SUM(product_lines.price * qty) as total")->one();
    return $lines['total'];
  }

  protected function onPaymentCompleted($order) {
    if (!$this->errors()) {
      $this->store(["id" => $order['id'], "order_status" => "pending"]);
      $this->queues->put(ConfirmOrder::class, ["order" => $order["id"]]);
    }
  }
}
