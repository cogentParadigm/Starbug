<?php
$text_leaf = $_POST["text_leaf-$leaf[container]-$leaf[position]"];
//echo "UDPATE ".P("text_leaf")." SET content='$text_leaf[content]' WHERE page='$leaf[page]' && container='$leaf[container]' && position='$leaf[position]'";
$sb->db->Execute("UPDATE `".P("text_leaf")."` SET content='$text_leaf[content]' WHERE page='$leaf[page]' && container='$leaf[container]' && position='$leaf[position]'");
?>
