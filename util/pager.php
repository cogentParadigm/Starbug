<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/pager.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup pager
 */
namespace Starbug\Core;
/**
 * @defgroup pager
 * pager utility
 * @ingroup util
 */
/**
 * pagination class
 * @ingroup pager
 */
class pager {
	var $count; //total number of items
	var $per; //items per page
	var $last; //last page
	var $start; // showing $start to $finish of $count items
	var $finish; // showing $start to $finish of $count items
	var $current_page; //current page
	var $range; //number of page links to show
	function __construct($total, $items_per_page, $current_pg=1, $range=6) {
		$this->count = $total;
		$this->per = $items_per_page;
		$this->range = $range;
		if (empty($current_pg) || !is_numeric($current_pg)) $current_pg = 1;
		$this->current_page = $current_pg;
		$this->last = ceil($total/$items_per_page);
		$this->start = (($current_pg-1)*$items_per_page);
		if ($this->current_page < 1) $this->current_page = 1;
		else if ($this->current_page > $this->last) $this->current_page = $this->last;
		$remaining = $total - $this->start;
		$shown = ($remaining<$items_per_page) ? ($remaining % $items_per_page) : $items_per_page;
		$this->finish = $this->start + $shown;
	}
}
