<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/TermsListCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class TermsListCollection extends TermsCollection {
	public function build($query, &$ops) {
		$query->sort("terms.term_path ASC, terms.position ASC");
		return $query;
	}
}
?>
