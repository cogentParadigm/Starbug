<?php
namespace Starbug\Payment;

use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\IdentityInterface;

class SelectPaymentCardCollection extends Collection {
  protected $model = "payment_cards";
  public function __construct(ModelFactoryInterface $models, IdentityInterface $user) {
    $this->models = $models;
    $this->user = $user;
  }
  public function build($query, &$ops) {
    $query->condition("payment_cards.owner", $this->user->userinfo("id"));
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
