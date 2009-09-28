<?php echo $sb->db->Execute("SELECT * FROM ".P("text_leaf")." WHERE page='$name' && container='$container' && position='$leaf[position]'")->fields("content"); ?>
