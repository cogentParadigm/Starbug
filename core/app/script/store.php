<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/store.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$name = array_shift($argv);
$params = join("  ", $argv);
$params = star($params);
$errors = store($name, $params);
if (empty($errors)) {
	$id = (empty($params['id'])) ? sb("insert_id") : $params['id'];
	$argv = array($name, "where:id='$id'");
	include(BASE_DIR."/core/app/script/query.php");
} else {
	foreach($errors as $col => $arr) {
		echo $col.":\n";
		foreach($arr as $e => $m) {
			echo "\t".$m."\n";
		}
	}
}
?>
