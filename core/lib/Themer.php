<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Themer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Themer
 */
/**
 * @defgroup Themer
 * Themer (experimental)
 * @ingroup lib
 */
$sb->provide("core/lib/Themer");
/**
 * Themer class
 * @ingroup Themer
 */
class Themer {
	var $enabled = array();
	
	function __construct() {
		$this->enabled = config("themes");
	}
	
	function enable($theme) {
		$this->enabled[] = $theme;
	}
	
	function disable($theme) {
		unset($this->enabled[array_search($theme, $this->enabled)]);
	}
}
