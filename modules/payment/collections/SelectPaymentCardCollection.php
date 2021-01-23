<?php
namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;

class SelectPaymentCardCollection extends Collection {
  protected $model = "payment_cards";
  public function __construct(ModelFactoryInterface $models, SessionHandlerInterface $session) {
    $this->models = $models;
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
