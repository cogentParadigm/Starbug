<?php
	function rmloc(&$arr, &$locarr) {
		if (($pos = current($locarr)) !== false) {
			if (next($locarr) === false) unset($arr[$pos]);
			else rmloc($arr[$pos], $locarr);
		}
	}
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	$filename = "core/db/schema/".ucwords($parts[0]);
	if (count($parts) == 1) unlink($filename);
	else {
		$arr = split("-", $parts[1]);
		$fields = unserialize(file_get_contents($filename));
		rmloc($fields, $arr);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
	}
?>