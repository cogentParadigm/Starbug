<?php
/**
* FILE: util/subscribe.php
* PURPOSE: sb mixin. adds subscriber functions
* 
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
$sb->provide("util/subscribe");
class Subscriber {
	# subscribe to a topic
	# @param topic - topic to subscribe to
	# @param tags - a string tag or array of tags to which your subscription applies. all uris are tagged 'global'
	# @param priority - method of controlling the execution order. All subscriptions made by starbug use 10
	# @param handle - static function handle. use 'sb::load' to include a file
	# @param $args - passed to the function
	function subscribe($topic, $tags, $priority, $handle, $args=null) {
		if (!is_array($tags)) $tags = array($tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists("var/hooks/$tag.$topic")) ? unserialize(file_get_contents("var/hooks/$tag.$topic")) : array();
			$subscriptions[$priority][] = ($args == null) ? array("handle" => $handle, "args" => array()) : array("handle" => $handle, "args" => $args);
			$file = fopen("var/hooks/$tag.$topic", "wb");
			fwrite($file, serialize($subscriptions));
			fclose($file);
		}
	}
	# unsubscribe to remove a subscription
	# @param topic - the topic you subscribed to
	# @param tags - the tags which you want to remove the subscription from
	# @param priority - the priority you used to subscribe
	# @param handle - the handle that is subscribed
	function unsubscribe($topic, $tags, $priority, $handle, $args=array()) {
		if (!is_array($tags)) $tags = array($tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists("var/hooks/$tag.$topic")) ? unserialize(file_get_contents("var/hooks/$tag.$topic")) : array();
			foreach($subscriptions[$priority] as $index => $ball) if (($ball['handle'] == $handle) && ($ball['args'] == $args)) unset($subscriptions[$priority][$index]);
			$file = fopen("var/hooks/$tag.$topic", "wb");
			fwrite($file, serialize($subscriptions));
			fclose($file);
		}
	}
	# get all subscriptions
	# @return an array where $arr[$tag][$topic] = $subscriptions
	function subscriptions() {
		$subs = array();
		if (false !== ($handle = opendir("var/hooks/"))) {
			while (false !== ($file = readdir($handle))) {
				if ((strpos($file, ".") !== 0)) {
					$parts = explode(".", $file);
					$tag = $parts[0];
					$topic = $parts[1];
					if (!isset($subs[$tag])) $subs[$tag] = array();
					$subs[$tag][$topic] = unserialize(file_get_contents("var/hooks/$file"));
				}
			}
			closedir($handle);
		}
		return $subs;
	}
}
$sb->mixin("Subscriber");
?>
