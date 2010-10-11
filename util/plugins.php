<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
$sb->import("util/subscribe");
$sb->provide("util/plugins");
class plugins {
	function info($plugin) {
		return $plugin;
	}
	function available($tag="") {
		$plugs = array();
		$active = (!empty($tag)) ? sb::publish($tag.".plugins") : sb::publish("plugins");
		if (false !== ($handle = opendir("core/app/plugins/"))) {
			while (false !== ($file = readdir($handle))) {
				if ((strpos($file, ".") !== 0)) {
					$plugs[$file] = array("active" => (false !== (array_search($file, $active))), "type" => "core");
				}
			}
			closedir($handle);
		}
		if (false !== ($handle = opendir("app/plugins/"))) {
			while (false !== ($file = readdir($handle))) {
				if ((strpos($file, ".") !== 0)) {
					$plugs[$file] = array("active" => (false !== (array_search($file, $active))), "type" => "app");
				}
			}
			closedir($handle);
		}
		return $plugs;
	}
	function load($plugin, $topic, $tag="global", $priority=10) {
		global $sb;
		$sb->subscribe("plugins", "global", $priority, "plugins::info", end(explode("/", $plugin)));
		$sb->subscribe($topic, $tag, $priority, "sb::load", $plugin);
	}
	function unload($plugin, $topic, $tag="global", $priority=10) {
		global $sb;
		$sb->unsubscribe($topic, $tag, $priority, "sb::load", $plugin);
		$sb->unsubscribe("plugins", "global", $priority, "plugins::info", end(explode("/", $plugin)));
	}
	function activate($plugin, $tag, $priority=10) {
		global $sb;
		$sb->subscribe("plugins", $tag, $priority, "return_it", $plugin);
	}
	function deactivate($plugin, $tag, $priority=10) {
		global $sb;
		$sb->unsubscribe("plugins", $tag, $priority, "return_it", $plugin);
	}
}
?>
