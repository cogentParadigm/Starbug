<?php
namespace Starbug\Users;

use Starbug\Core\Collection;

class AdminUsersCollection extends Collection {
  public $model = "users";
  public function build($query, &$ops) {
    $query->select("users.*");
    $query->select("GROUP_CONCAT(users.groups.term SEPARATOR ', ') as groups");
    $query->select("IF(users.deleted=1, 'Deleted', 'Active') as deleted");
    if (!empty($ops['group']) && is_numeric($ops['group'])) {
      $query->condition("users.groups.id", $ops['group']);
    } elseif (!empty($ops["groups"])) {
      $query->condition("users.groups.id", explode(",", $ops['groups']));
    }
    if (isset($ops['deleted'])) {
      $query->condition("users.deleted", explode(",", $ops['deleted']));
    } else {
      $query->condition("users.deleted", "0");
    }
    $query->group("users.id");
    return $query;
  }
}
