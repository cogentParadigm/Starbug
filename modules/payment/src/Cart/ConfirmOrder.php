<?php
namespace Starbug\Payment\Cart;

use Starbug\Db\DatabaseInterface;
use Starbug\Emails\MailerInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Payment\PriceFormatterInterface;
use Starbug\Queue\TaskInterface;
use Starbug\Queue\QueueInterface;
use Starbug\Queue\WorkerInterface;

class ConfirmOrder implements WorkerInterface {
  public function __construct(DatabaseInterface $db, MailerInterface $mailer, PriceFormatterInterface $priceFormatter, SessionHandlerInterface $session) {
    $this->db = $db;
    $this->mailer = $mailer;
    $this->priceFormatter = $priceFormatter;
    $this->session = $session;
  }
  public function process(TaskInterface $task, QueueInterface $queue) {
    $id = $task->getData()["order"];
    $order = $this->db->query("orders")->condition("id", $id)->one();
    $lines = $this->db->query("product_lines")
      ->innerJoin("lines")->on("lines.id=product_lines.lines_id")
      ->condition("lines.orders_id", $order["id"])
      ->select(["description", "qty", "price"], "lines")
      ->all();
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
      "<p><strong>Order Total:</strong> ".$this->priceFormatter->format($order["total"])."</p>"
    ]);
    $data = [
      "user" => $this->session->getData(),
      "order" => $order
    ];
    $this->mailer->send(["template" => "Order Confirmation", "to" => $order["email"]], $data);
    $queue->complete($task);
  }
}
