<?php
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
	function subscribe($topic, $tags, $priority, $handle, $args=null) {
		if (!is_array($tags)) $tags = array($tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists(BASE_DIR."/app/hooks/$tag.$topic")) ? unserialize(file_get_contents(BASE_DIR."/app/hooks/$tag.$topic")) : array();
			$subscriptions[$priority][] = ($args == null) ? array("handle" => $handle, "args" => array()) : array("handle" => $handle, "args" => $args);
			$file = fopen(BASE_DIR."/app/hooks/$tag.$topic", "wb");
			fwrite($file, serialize($subscriptions));
			fclose($file);
			chmod(BASE_DIR."/app/hooks/$tag.$topic", 0666);
		}
	}
	/**
	 * unsubscribe to remove a subscription
	 * @param topic - the topic you subscribed to
	 * @param tags - the tags which you want to remove the subscription from
	 * @param priority - the priority you used to subscribe
	 * @param handle - the handle that is subscribed
	 */
	function unsubscribe($topic, $tags, $priority, $handle, $args=array()) {
		if (!is_array($tags)) $tags = array($tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists(BASE_DIR."/app/hooks/$tag.$topic")) ? unserialize(file_get_contents(BASE_DIR."/app/hooks/$tag.$topic")) : array();
			foreach($subscriptions[$priority] as $index => $ball) if (($ball['handle'] == $handle) && ($ball['args'] == $args)) unset($subscriptions[$priority][$index]);
			$file = fopen(BASE_DIR."/app/hooks/$tag.$topic", "wb");
			fwrite($file, serialize($subscriptions));
			fclose($file);
		}
	}
	/**
	 * get all subscriptions
	 * @return an array where $arr[$tag][$topic] = $subscriptions
	 */
	function subscriptions() {
		$subs = array();
		if (false !== ($handle = opendir(BASE_DIR."/app/hooks/"))) {
			while (false !== ($file = readdir($handle))) {
				if ((strpos($file, ".") !== 0)) {
					$parts = explode(".", $file);
					$tag = $parts[0];
					$topic = $parts[1];
					if (!isset($subs[$tag])) $subs[$tag] = array();
					$subs[$tag][$topic] = unserialize(file_get_contents(BASE_DIR."/app/hooks/$file"));
				}
			}
			closedir($handle);
		}
		return $subs;
	}
}
$sb->mixin("Subscriber");
?>
