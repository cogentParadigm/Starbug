<?php
namespace Starbug\Users\Collection;

use Starbug\Core\AdminCollection;

class AdminUsersCollection extends AdminCollection {
  public $model = "users";
  public function build($query, $ops) {
    if (empty($ops["sort"])) {
      $ops["sort"] = "last_visit DESC";
    }
    $query->select("users.*");
    $query->select("GROUP_CONCAT(users.groups.term SEPARATOR ', ') as groups");
    $query->select("IF(users.deleted=1, 'Deleted', 'Active') as deleted");
    if (!empty($ops['group']) && is_numeric($ops['group'])) {
      $query->condition("users.groups.id", $ops['group']);
    } elseif (!empty($ops["groups"])) {
      $query->condition("users.groups.id", explode(",", $ops['groups']));
    }
    $query->group("users.id");
    return parent::build($query, $ops);
  }
}
