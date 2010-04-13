<?php
$sb->provide("util/cli");
function query($model, $args="") {
	global $sb;
	cli::table($sb->query($model, $args));
}
class cli {
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
