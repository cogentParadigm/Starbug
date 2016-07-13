<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/TermsCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class TermsCollection extends Collection {
	public function build($query, &$ops) {
		return $query;
	}
	public function filterQuery($query, &$ops) {
		parent::filterQuery($query, $ops);
		if (!empty($ops['taxonomy'])) {
			$query->condition("terms.taxonomy", $ops['taxonomy']);
		}
		return $query;
	}
}
?>
