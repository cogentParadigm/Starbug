<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/ModelsCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class ModelsCollection extends Collection {
	public $model = "entities";
	public function build($query, &$ops) {
		$query->undo("select");
		$query->select("entities.name as id,entities.label");
		return $query;
	}
}
?>
