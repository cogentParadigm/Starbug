<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/files/collections/AdminFilesCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Files;
use Starbug\Core\Collection;
class AdminFilesCollection extends Collection {
	public $model = "files";
	public function build($query, &$ops) {
		$query->condition("files.statuses.slug", "deleted", "!=", array("ornull" => true));
		if (!empty($ops['category']) && is_numeric($ops['category'])) {
			$query->condition("category", $ops['category']);
		}
		return $query;
	}
}
?>
