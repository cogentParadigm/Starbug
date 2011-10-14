<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/subscribe.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup subscribe
 */
/**
 * @defgroup subscribe
 * subscribe utility
 * @ingroup util
 */
$sb->provide("util/subscribe");
/**
 * sb mixin to add subscriber functions
 * @ingroup subscribe
 */
class Subscriber {
	/**
	 * subscribe to a topic
	 * @param topic - topic to subscribe to
	 * @param tags - a string tag or array of tags to which your subscription applies. all uris are tagged 'global'
	 * @param priority - method of controlling the execution order. All subscriptions made by starbug use 10
	 * @param handle - static function handle. use 'sb::load' to include a file
	 * @param $args - passed to the function
	 */
	function subscribe($topic, $handle, $args=array(), $tags="global") {
		if (!is_array($tags)) $tags = explode(",", $tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists(BASE_DIR."/etc/hooks/$tag.$topic.json")) ? json_decode(file_get_contents(BASE_DIR."/etc/hooks/$tag.$topic.json"), true) : array();
			$subscriptions[] = array("handle" => $handle, "args" => $args);
			file_put_contents(BASE_DIR."/etc/hooks/$tag.$topic.json", json_encode($subscriptions));
			chmod(BASE_DIR."/etc/hooks/$tag.$topic", 0666);
		}
	}
	/**
	 * unsubscribe to remove a subscription
	 * @param topic - the topic you subscribed to
	 * @param tags - the tags which you want to remove the subscription from
	 * @param priority - the priority you used to subscribe
	 * @param handle - the handle that is subscribed
	 */
	function unsubscribe($topic, $handle, $args=array(), $tags="global") {
		if (!is_array($tags)) $tags = explode(",", $tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists(BASE_DIR."/etc/hooks/$tag.$topic.json")) ? json_decode(file_get_contents(BASE_DIR."/etc/hooks/$tag.$topic.json"), true) : array();
			foreach($subscriptions as $index => $ball) if (($ball['handle'] == $handle) && ($ball['args'] == $args)) unset($subscriptions[$index]);
			file_put_contents(BASE_DIR."/etc/hooks/$tag.$topic.json", json_encode($subscriptions));
		}
	}
	/**
	 * get all subscriptions
	 * @return an array where $arr[$tag][$topic] = $subscriptions
	 */
	function subscriptions() {
		$subs = array();
		if (false !== ($handle = opendir(BASE_DIR."/etc/hooks/"))) {
			while (false !== ($file = readdir($handle))) {
				if ((strpos($file, ".") !== 0)) {
					$parts = explode(".", $file);
					$tag = $parts[0];
					$topic = $parts[1];
					if (!isset($subs[$tag])) $subs[$tag] = array();
					$subs[$tag][$topic] = json_decode(file_get_contents(BASE_DIR."/etc/hooks/$file"), true);
				}
			}
			closedir($handle);
		}
		return $subs;
	}
}
$sb->mixin("Subscriber");
?>
