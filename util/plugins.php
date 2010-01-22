<?php
$sb->import("util/subscribe");
$sb->provide("util/plugins");
class plugins {
	function info($plugin) {
		return $plugin;
	}
	function available() {
		$plugs = array();
		$active = sb::publish("plugins");
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
	function activate($plugin, $topic, $priority=10, $tag="global") {
		global $sb;
		$sb->subscribe("plugins", $tag, $priority, "plugins::info", end(explode("/", $plugin)));
		$sb->subscribe($topic, $tag, $priority, "sb::load", $plugin);
	}
	function deactivate($plugin, $topic, $priority=10, $tag="global") {
		global $sb;
		$sb->unsubscribe($topic, $tag, $priority, "sb::load", $plugin);
		$sb->unsubscribe("plugins", $tag, $priority, "plugins::info", end(explode("/", $plugin)));
	}
}
?>
