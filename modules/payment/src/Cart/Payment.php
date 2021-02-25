<?php
namespace Starbug\Payment\Cart;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\MailerInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;
use Starbug\Payment\Cart;
use Starbug\Payment\GatewayInterface;
use Starbug\Payment\PriceFormatterInterface;
use Starbug\Payment\TokenGatewayInterface;

class Payment extends Save {
  protected $model = "orders";
  public function __construct(ModelFactoryInterface $models, Cart $cart, SessionHandlerInterface $session, MailerInterface $mailer, PriceFormatterInterface $priceFormatter, GatewayInterface $gateway, TokenGatewayInterface $subscriptions) {
    $this->models = $models;
    $this->cart = $cart;
    $this->session = $session;
    $this->mailer = $mailer;
    $this->priceFormatter = $priceFormatter;
    $this->gateway = $gateway;
    $this->subscriptions = $subscriptions;
  }
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $payment = $data->get();
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
    $order_total = 0;
    $ammend = ["id" => $order['id'], "email" => $payment['email'], "phone" => $payment['phone']];
    if ($this->session->loggedIn()) {
      $ammend['owner'] = $this->session->getUserId();
    }

    // determine single payment amount
    // WARN: prices not validated, lines could be stale
    $lines = $this->query("product_lines")
      ->condition("orders_id", $order['id'])
      ->condition("product_lines.product.payment_type", "single")
      ->select("SUM(product_lines.price * qty) as total")->one();
    $total = $lines['total'];
    if ($total) {
      $order_total += $total;
      $ammend["total"] = $total;
      $this->purchase($payment + ["amount" => $total, "orders_id" => $order["id"]]);
    }

    // determine recurring payment amounts
    $lines = $this->query("product_lines")
      ->condition("orders_id", $order['id'])
      ->condition("product_lines.product.payment_type", "recurring")->all();
    foreach ($lines as $line) {
      $price = $line["price"] * $line["qty"];
      $order_total += $price;
      $this->purchase($payment + ["amount" => $price, "orders_id" => $order["id"]]);
      if (!$this->errors()) {
        $this->subscriptions->createSubscription(["orders_id" => $order["id"], "amount" => $price, "product" => $line["product"], "payment" => $this->models->get("payments")->insert_id] + $payment);
      }
    }

    $this->store($ammend);
    if (!$this->errors()) {
      $this->store(["id" => $order['id'], "order_status" => "pending"]);
      $lines = $this->query("product_lines")->condition("orders_id", $order["id"])->all();
      $order["description"] = $lines[0]["description"];
      $count = count($lines) - 1;
      if ($count > 1) {
        $order["description"] .= " and ".$count." other items";
      } elseif ($count > 0) {
        $order["description"] .= " and 1 other item";
      }
      $rows = [];
      foreach ($lines as $line) {
        $rows[] = "<tr><td>".$line["description"]."</td><td>".$line["qty"]."</td><td>".$this->priceFormatter->format($line["price"]*$line["qty"])."</td></tr>";
      }
      $order["details"] = implode("\n", [
        "<p>Order #".$order["id"]."</p>",
        "<table>",
        "<tr><th>Product</th><th>Qty</th><th>Total</th></tr>",
        implode("\n", $rows),
        "</table>",
        "<p><strong>Order Total:</strong> ".$this->priceFormatter->format($order_total)."</p>"
      ]);
      $data = [
        "user" => $this->session->getData(),
        "order" => $order
      ];
      $this->mailer->send(["template" => "Order Confirmation", "to" => $payment["email"]], $data);
    }
    return $this->getErrorState($state);
  }
  protected function purchase($payment) {
    if (empty($payment["card"])) {
      $this->gateway->purchase($payment);
    } else {
      $this->subscriptions->purchase($payment);
    }
  }
}