<?php
$leaf = explode(" ", $leaf);
$sb->db->Execute("DELETE FROM `".P("leafs")."` WHERE page='$pagename' && container='$container' && position='".$leaf[0]."'");
$sb->db->Execute("DELETE FROM `".P("text_leaf")."` WHERE page='$pagename' && container='$container' && position='".$leaf[0]."'");
?>
