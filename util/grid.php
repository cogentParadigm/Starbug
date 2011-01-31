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
 * @ingroup grid
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
		efault($ops['dojoType'], "starbug.grid.EnhancedGrid");
		if (!empty($ops['orderColumn'])) efault($ops['plugins'], "{nestedSorting: true, cellFormatter: true, dnd: true}");
		else efault($ops['plugins'], "{nestedSorting: true, cellFormatter: true}");
		$this->ops = $ops;
		$this->query($query);
	}
	function add_column($col, $caption="") {
		$col = starr::star($col);
		efault($caption, ucwords($col[0]));
		$col['content'] = $caption;
		$tag = "field:".array_shift($col);
		foreach ($col as $k=> $v) $tag .= "  $k:$v";
		$this->headers[] = $tag;
	}
	function query($query) {
		$this->ops['storeUrl'] = uri("api/".$this->ops['models']."/get.json?query=".base64_encode($query));
	}
	function render() {
		$tag = "";
		foreach ($this->ops as $k => $v) $tag .= "  $k:$v";
		$output = str_replace("</table>", "", tag("table  echo:false$tag"));
		$output .= "<thead><tr>";
		foreach ($this->headers as $col) $output .= tag("th  $col  echo:false");
		$output .= "</tr></thead></table>";
		echo $output;
	}
}
