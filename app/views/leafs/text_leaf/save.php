<?php
$text_leaf = $_POST["text_leaf-$leaf[container]-$leaf[position]"];
$sb->db->exec("UPDATE `".P("text_leaf")."` SET content='$text_leaf[content]' WHERE page='$leaf[page]' && container='$leaf[container]' && position='$leaf[position]'");
?>
