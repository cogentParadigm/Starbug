<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Themer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
$sb->provide("core/lib/Themer");
/**
 * Themer class
 * @ingroup core
 */
class Themer {
	var $enabled = array();
	
	function __construct() {
		global $sb;
		$this->enabled = $sb->publish("themes");
	}
	
	function enable($theme) {
		global $sb;
		$sb->import("util/subscribe");
		$sb->subscribe("themes", "global", 10, "return_it", $theme);
	}
	
	function disable($theme) {
		global $sb;
		$sb->import("util/subscribe");
		$sb->unsubscribe("themes", "global", 10, "return_it", $theme);
	}
}
