<?php
/**
 * This file is part of StarbugPHP
 * @file util/cli.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup cli
 */
/**
 * @defgroup cli
 * cli output utility
 * @ingroup util
 */
$sb->provide("util/cli");
/**
 * query the database and output the results in a table formatted for cli output
 * @see sb::query
 * @see cli::table
 * @ingroup cli
 */
function cli_query($model, $args="", $mine=false) {
	global $sb;
	cli::table($sb->query($model, $args, $mine));
}
/**
 * class for cli output functions
 * @ingroup cli
 */
class cli {
/**
 * output an array of records in a table formatted for command line output
 * @param array $records an array of records
 */
function table($records) {
		$lengths = array();
		$one = $two = $three = "";
		foreach($records[0] as $key => $value) {
			$current = strlen($key);
			$lengths[$key] = $current;
			foreach ($records as $r) {
				$l = strlen($r[$key]);
				if ($l > $lengths[$key]) $lengths[$key] = $l;
			}
			$colsize = $lengths[$key];
			$dif = $colsize - $current;
			for($i=0;$i<$colsize;$i++) {
				$one .= "-";
				$three .= "-";
			}
			$two .= $key;
			for($i=0;$i<$dif;$i++) $two .= " ";
			$one .= "---";
			$three .= "---";
			$two .= " | ";
			
		}
		echo $one."\n".$two."\n".$three."\n";
		foreach($records as $r) {
			foreach ($r as $k => $v) {
				$colsize = $lengths[$k];
				$current = strlen($v);
				$dif = $colsize - $current;
				echo $v;
				for($i=0;$i<$dif;$i++) echo " ";
				echo " | ";
			}
			echo "\n";
		}
		echo "\n";
	}
}
?>
