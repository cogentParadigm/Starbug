<?php
namespace Starbug\Notifications\Recipient;

use Starbug\Db\DatabaseInterface;

class User implements RecipientTypeInterface {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function getId($notification) {
    return $notification["users_id"];
  }
  public function getRecipient($notification) {
    return $this->db->get("users", $notification["users_id"]);
  }
}
