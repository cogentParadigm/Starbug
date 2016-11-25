<?php
namespace Starbug\Users;
use Starbug\Core\Collection;
class AdminUsersCollection extends Collection {
	public $model = "users";
	public function build($query, &$ops) {
		$query->select("users.*");
		$query->select("users.groups.id as groups");
		if (!empty($ops['group']) && is_numeric($ops['group'])) {
			$query->condition("users.groups.id", $ops['group']);
		}
		if (!empty($ops['deleted'])) $query->condition("users.deleted", $ops['deleted']);
		else $query->condition("users.deleted", "0");
		return $query;
	}
}
?>
