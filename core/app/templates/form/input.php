<? if($type=="file") $file = query("files", "where:id=?  limit:1", array($form->get($name))); ?>
<?php if (!empty($file) && (reset(explode("/", $file['mime_type'])) == "image")) { ?>
		<img src="<?php echo image_thumb(uri("app/public/uploads/".$file['id']."_".$file['filename']), "w:100  h:100  a:1"); ?>"/>
<?php } ?>
<input <? html_attributes($attributes); ?>/>
<? if (!empty($file)) { ?>
<div class="field"><?= $file['filename']; ?></div>
<? } ?>
