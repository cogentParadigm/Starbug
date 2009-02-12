<h2>Models</h2>
<?php include("core/public/js/models.php"); ?>
<script type="text/javascript">
	function showhide(item) {
		var node = dojo.byId(item);
		var display = node.getAttribute('class');
		if (display == 'hidden') display = '';
		else display = 'hidden';
		node.setAttribute('class', display);
	}
</script>
<?php
	include("core/app/models/Models.php");
	$models_object = new Models("core/db/schema/");
	$models = $models_object->get_all();
?>
<ul id="models" class="lidls">
<?php foreach ($models as $name => $fields) { $has = $this->has($name); $backup = file_exists("app/models/.".ucwords($name));?>
	<li id="<?php echo $name; ?>"<?php if (!$has) echo " class=\"inactive\""; ?>>
		<h3>
			<a href="" onclick="if (confirm('Are you sure you want to delete?')) {delete_model('<?php echo $name; ?>');return false;}">[X]</a>
			<?php if ($has) { ?>
				<a href="" onclick="deactivate_model('<?php echo $name; ?>');return false;">[deactivate]</a>
				<form class="hidden" id="deactivate_<?php echo $name; ?>" method="post">
					<input type="hidden" name="deactivate_model" value="1" />
				</form>
				<a href="" onclick="">[update]</a>
			<?php } else { ?>
				<form class="hidden" id="activate_<?php echo $name; ?>" method="post">
					<input type="hidden" name="activate_model" value="1" />
					<input id="restore_backup" type="hidden" name="restore_backup" value="<?php echo $backup; ?>"/>
				</form>
				<a href="" onclick="activate_model('<?php echo $name; ?>', '<?php echo $backup; ?>');return false;">[activate]</a>
			<?php } ?>
			<a href="" class="title" onclick="showhide('<?php echo $name; ?>_model');return false;"><?php echo $name; ?></a>
		</h3>
		<div id="<?php echo $name; ?>_model" class="hidden" style="padding:5px">
			<?php echo Models::dlfields($fields, $name); ?>
			<a href="" class="button clear" onclick="new_field('<?php echo $name; ?>');return false;">new field</a>
		</div>
	</li>
<?php } ?>
</ul>
<a href="models/create" onclick="new_model();return false;" class="button">new model</a>