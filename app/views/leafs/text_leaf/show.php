<?php $leaf = $sb->query("text_leaf", "where:page='$name' && container='$container' && position='$leaf[position]'  limit:1"); echo $leaf['content']; ?>
