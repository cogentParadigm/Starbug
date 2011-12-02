<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/console.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup console
 */
/**
 * @defgroup console
 * error logging utility
 * @ingroup lib
 */
$sb->provide("core/lib/console");
/**
 * allows errors and notifications to be logged to the console
 * @ingroup console
 */
class console {
	function log($item, $type="") {
		if (is_string($item)) {
			efault($type, "string");
			store("errors", array("action" => "log", "type" => $type, "message" => $item));
		} else if (is_numeric($item)) {
			efault($type, "int");
			store("errors", array("action" => "log", "type" => $type, "message" => $item));
		} else if (is_bool($item)) {
			efault($type, "boolean");
			store("errors", array("action" => "log", "type" => $type, "message" => $item));
		} else if (is_array($item)) {
			ob_start();
			print_r($item);
			$item = ob_get_contents();
			ob_end_clean();
			console::log($item,
		} else {
			ob_start();
			var_dump($item);
			$item = ob_get_contents();
			ob_end_clean();
		}
	}
	store($item, $type="primitive") {
		store("errors", array("action" => "log", "type" => $type, "message" => $item));
	}
}
