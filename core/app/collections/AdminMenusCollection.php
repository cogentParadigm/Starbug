<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/AdminMenusCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class AdminMenusCollection extends Collection {
	public function build($query, &$ops) {
		$query->undo("select");
		$query->select("DISTINCT menu");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as &$row) {
			$row['id'] = $row['menu'];
		}
		return $rows;
	}
}
?>
