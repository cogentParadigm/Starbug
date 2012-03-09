<input <? html_attributes($attributes); ?>/>
<? if($type=="file") { $file = query("files", "where:id=?  limit:1", array($form->get($name))); if (!empty($file)) { ?>
<div class="field"><?= $file['filename']; ?></div>
<? } } ?>
