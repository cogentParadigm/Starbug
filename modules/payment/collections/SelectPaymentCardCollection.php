<?php
namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Collection;
use Starbug\Core\DatabaseInterface;

class SelectPaymentCardCollection extends Collection {
  protected $model = "payment_cards";
  public function __construct(DatabaseInterface $db, SessionHandlerInterface $session) {
    $this->db = $db;
    $this->session = $session;
  }
  public function build($query, $ops) {
    $query->condition("payment_cards.owner", $this->session->getUserId());
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row["label"] = ucwords($row["brand"])." xxxx".$row["number"];
    }
    $rows[] = ["id" => "", "label" => "Add a new card.."];
    return $rows;
  }
}
