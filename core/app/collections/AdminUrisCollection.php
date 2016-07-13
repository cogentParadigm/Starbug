<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/AdminUrisCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class AdminUrisCollection extends Collection {
	public function build($query, &$ops) {
		$query->select($query->model.".statuses.term as statuses");
		if (!empty($ops['type'])) {
			$query->condition($query->model.".type", $ops['type']);
		}
		if (!empty($ops['status'])) $query->condition($query->model.".statuses.id", $ops['status']);
		else $query->condition($query->model.".statuses.slug", "deleted", "!=");
		if (empty($ops['orderby'])) $ops['orderby'] = "modified DESC, created DESC, title DESC";
		$query->sort($ops['orderby']);
		return $query;
	}
}
?>
