<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/dojo.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup dojo
 */
/**
 * @defgroup dojo
 * global functions
 * @ingroup global
 */
/**
 * connect javascript events to css selectors
 * @ingroup dojo
 * @param star $ops the css selector, action: the js function, url: optional url for xhr submission, event: js event (default onclick)
 * @param star $params additional paramteres to be passed to your function
 */
function connect($ops, $params="") {
	$ops = star($ops);
	$query = array_shift($ops);
	efault($ops['event'], 'onclick');
	global $sb;
	$sb->import("util/dojo");
	global $dojo;
	if (isset($ops['url'])) $dojo->xhr($query, $ops['action'], $ops['url'], $params, $ops['event']);
	$dojo->attach($query, $ops['action'], $params, $ops['event']);
}
function js($mid) {
	global $sb;
	$sb->import("util/dojo");
	global $dojo;
	$dojo->require_js($mid);
}
?>
