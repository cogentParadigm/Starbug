<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/SelectCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;

class SelectCollection extends Collection {
	public function build($query, &$ops) {
		$query->undo("select");
		if (empty($ops['id'])) {
			$query->condition($query->model.".statuses.slug", "deleted", "!=", array("ornull" => true));
		}
		$query->select($query->model.".id");
		$query->select($this->models->get($this->model)->label_select." as label");
		return $query;
	}
}
?>
