<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/grid.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup grid
 */
/**
 * @defgroup grid
 * grid utility
 * @ingroup util
 */
$sb->provide("util/grid");
/**
 * outputs lists of data in an HTML table
 * @ingroup grid
 */
class grid {
	var $headers;
	var $ops;
	function __construct($ops, $query) {
		$ops = starr::star($ops);
		efault($ops['id'], $ops['model']."_grid");
		efault($ops['models'], $ops['model']);
		efault($ops['jsId'], $ops['id']);
		efault($ops['style'], "width:100%");
		efault($ops['autoHeight'], "100");
		efault($ops['rowsPerPage'], "100");
		efault($ops['dojoType'], "starbug.grid.EnhancedGrid");
		if (!empty($ops['orderColumn'])) efault($ops['plugins'], "{nestedSorting: true, dnd: true}");
		else efault($ops['plugins'], "{nestedSorting: true}");
		$ops['apiQuery'] = base64_encode($query);
		$this->ops = $ops;
	}
	function add_column($col, $caption="") {
		$col = starr::star($col);
		efault($caption, ucwords(str_replace("_", " ", $col[0])));
		$col['caption'] = $caption;
		$col['field'] = array_shift($col);
		$this->headers[] = $col;
	}
	function render() {
		assign("options", $this->ops);
		assign("columns", $this->headers);
		render("grid");
	}
}
