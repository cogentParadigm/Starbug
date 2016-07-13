<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/AdminCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class AdminCollection extends Collection {
	public function build($query, &$ops) {
		return $query;
	}
}
?>
