<?php
$_POST["text_leaf-$container-$leaf[position]"] = $sb->query("text_leaf", "where:page='$name' && container='$container' && position='$leaf[position]'	limit:1");
$form = new form("text_leaf-$container-$leaf[position]");
$fields = "<div class=\"field\">".$form->textarea("content	label:Text Leaf	id:$container-$leaf[position]	cols:107	rows:20")."</div>\n";
?>
