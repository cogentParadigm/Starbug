<?php
$leaf = explode(" ", $leaf);
$sb->remove("leafs", "page='$pagename' && container='$container' && position='".$leaf[0]."'");
$sb->remove("text_leaf", "page='$pagename' && container='$container' && position='".$leaf[0]."'");
?>
