<div class="multiple_category_select">
	<?php foreach ($terms as $term) { ?>
		<div class="category" style="padding-left:<?php echo $term['depth']*15; ?>px">
			<input <? html_attributes("type:checkbox  class:left checkbox  name:".$name."[]  value:$term[id]".((in_array($term['id'], $value)) ? "  checked:checked" : "")); ?>/><label><?= $term['term']; ?></label>
		</div>
	<?php } ?>
</div>
<?php if (end($options) == -1) { ?>
	<div id="<?php echo $id; ?>_new_category"<?php if ($value != -1) echo ' style="display:none"'; ?>>
		<? echo $form->text($field."_new_category  label:New Category"); ?>
		<br class="clear"/>
	</div>
<?php } ?>
