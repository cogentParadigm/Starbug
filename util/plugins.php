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
	function load($plugin, $topic, $tag="global") {
		global $sb;
		$sb->subscribe("plugins", "plugins::info", end(explode("/", $plugin)), $tag);
		$sb->subscribe($topic, "sb::load", $plugin, $tag);
	}
	function unload($plugin, $topic, $tag="global") {
		global $sb;
		$sb->unsubscribe($topic, "sb::load", $plugin, $tag);
		$sb->unsubscribe("plugins", "plugins::info", end(explode("/", $plugin)), $tag);
	}
	function activate($plugin, $tag="global") {
		global $sb;
		$sb->subscribe("plugins", "return_it", $plugin, $tag);
	}
	function deactivate($plugin, $tag="global") {
		global $sb;
		$sb->unsubscribe("plugins", "return_it", $plugin, $tag);
	}
}
?>
