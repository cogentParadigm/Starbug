<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/js/global_functions.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup js
 */
/**
 * @defgroup js
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
	$sb->import("core/lib/js");
	global $js;
	if (isset($ops['url'])) $js->xhr($query, $ops['action'], $ops['url'], $params, $ops['event']);
	$js->attach($query, $ops['action'], $params, $ops['event']);
	return $js;
}
function js($mid) {
	global $sb;
	$sb->import("core/lib/js");
	global $js;
	$js->require_js($mid);
	return $js;
}
function column($field, $ops=array()) {
	sb()->import("core/lib/js");
	global $js;
	return $js->column($field, $ops);
}
?>
