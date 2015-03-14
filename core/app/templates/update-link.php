<?php $schema = schema($model); ?>
<a class="edit button" href="<?php echo uri(str_replace("[action]", "update", $path)."/$id$to"); ?>"><img src="<?php echo uri("core/app/public/icons/file-edit.png"); ?>"/></a>
