<?php
$max = $sb->query("leafs", "select:MAX(position) as position	where:page='$pagename' && container='$container'	limit:1");
$position = (!is_numeric($max['position'])) ? 0 : $max['position']+1;
$l = array("leaf" => "text_leaf", "page" => $pagename, "container" => $container, "position" => "$position");
$e = $sb->store("leafs", $l);
if (empty($e)) $sb->db->Execute("INSERT INTO `".P("text_leaf")."` (page, container, position, content) VALUES ('$pagename', '$container', '$position', ''");
?>
