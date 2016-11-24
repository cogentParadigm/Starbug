<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/TaxonomyInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
/**
 * a simple interface for accessing taxonomy trees, and applying categories and tags
 */
interface TaxonomyInterface {
	function terms($taxonomy, $parent = 0, $depth = 0);
	/**
	 * apply tags
	 * @ingroup taxonomy
	 * @param string $taxonomy the taxonomy/classification of terms. eg. products_tags
	 * @param int $object_id the id of the object to apply the tag to
	 * @param string $tag the tag
	 * @return bool returns true on success, false otherwise.
	 */
	function tag($table, $object_id, $field, $tag = "");
	/**
	 * remove tags
	 * @ingroup taxonomy
	 * @param string $table (optional) the table to which tags are applied. This is only needed if not implied by $taxonomy
	 * @param string $taxonomy the taxonomy/classification of terms. eg. products_tags or genres
	 * @param int $object_id the id of the object to apply the tag to
	 * @param string $tag the tag
	 */
	function untag($table, $object_id, $field, $tag = "");
}
