<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/AdminTermsCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class AdminTermsCollection extends TermsCollection {
	public function build($query, &$ops) {
		$query->undo("select");
		$query->select("DISTINCT terms.taxonomy");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as &$row) {
			$row['id'] = $row['taxonomy'];
		}
		return $rows;
	}
}
?>
