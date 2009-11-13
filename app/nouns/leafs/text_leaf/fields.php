<?php
$_POST["text_leaf-$container-$leaf[position]"] = $sb->query("text_leaf", "where:page='$name' && container='$container' && position='$leaf[position]'	limit:1");
$contents = array("content" => "textarea	label:Text Leaf	id:$container-$leaf[position]	cols:107");
$extras = array($contents);
$fields = form::fields("text_leaf-$container-$leaf[position]", array("div	class:field	fields:$0"), $extras);
?>
