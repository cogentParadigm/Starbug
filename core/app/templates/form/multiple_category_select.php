<div class="multiple_category_select">
	<?php efault($value, array()); foreach ($terms as $term) { ?>
		<div class="form-group checkbox" style="padding-left:<?php echo $term['depth']*15; ?>px">
			<label><input <? html_attributes("type:checkbox  class:left checkbox  name:".$name."[]  value:$term[id]".((in_array($term['id'], $value)) ? "  checked:checked" : "")); ?>/><?= $term['term']; ?></label>
		</div>
	<?php } ?>
	<input <? html_attributes("type:hidden  name:".$name."[]  value:-~"); ?>/>
</div>
<?php if ($writable) { ?>
	<div id="<?php echo $id; ?>_new_category"<?php if ($value != -1) echo ' style="display:none"'; ?>>
		<? echo $form->text($field."_new_category  label:New Category"); ?>
		<br class="clear"/>
	</div>
<?php } ?>
