<input<? foreach ($attributes as $key => $value) if (!empty($value) && !is_array($value) && !in_array($key, array("label", "field"))) echo ' '.$key.'="'.$value.'"'; ?>/>
<? if($type=="file") { $file = query("files", "where:id=?  limit:1", array($form->get($name))); if (!empty($file)) { ?>
<div class="field"><?= $file['filename']; ?></div>
<? } ?>
