<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/users/collections/AdminUsersCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Users;
use Starbug\Core\Collection;
class AdminUsersCollection extends Collection {
	public $model = "users";
	public function build($query, &$ops) {
		$query->select("users.*");
		$query->select("users.groups.id as groups");
		$query->select("users.statuses.id as statuses");
		if (!empty($ops['group']) && is_numeric($ops['group'])) {
			$query->condition("users.groups.id", $ops['group']);
		}
		if (!empty($ops['status']) && is_numeric($ops['status'])) $query->condition("users.statuses.id", $ops['status']);
		else $query->condition("users.statuses.slug", "deleted", "!=", array("ornull" => true));
		return $query;
	}
}
?>
