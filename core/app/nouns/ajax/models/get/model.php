<?php
	include("core/app/models/Models.php");
	$models = new Models($sb->db);
	if (!empty($_POST['new_model'])) {
		$models->create($_POST['modelname']);
?>
<li id="<?php echo $_POST['modelname']; ?>" class="inactive">
	<h3>
		<a href="" onclick="delete_model('<?php echo $_POST['modelname']; ?>');return false;">[X]</a>
		<a href="" onclick="">[activate]</a>
		<a href="" class="title" onclick="showhide('<?php echo $_POST['modelname']; ?>_model');return false;"><?php echo $_POST['modelname']; ?></a>
	</h3>
	<div id="<?php echo $_POST['modelname']; ?>_model" class="hidden" style="padding:5px;">
		<dl id="<?php echo $_POST['modelname']; ?>-fields">
		</dl>
		<a class="button clear" href="" onclick="new_field('<?php echo $_POST['modelname']; ?>');return false;">new field</a>
	</div>
</li>
<?php } else if (!empty($_POST['activate_model']) || !empty($_POST['deactivate_model']) || !empty($_POST['activate_field'])) {
	$name = next($this->uri);
	if (!empty($_POST['activate_model'])) {
		$models->activate($name, $_POST['restore_backup']);
	} else if (!empty($_POST['deactivate_model'])) {
		$models->deactivate($name);
	} else if (!empty($_POST['activate_field'])) {
		$models->activate($name);
		$name = reset(split("-", $name));
	}	
	$has = $models->is_active($name);
	$fields = $models->get($name);
?>
<h3>
	<a href="" onclick="delete_model('<?php echo $name; ?>');return false;">[X]</a>
	<?php if ($has) { ?>
				<a href="" onclick="deactivate_model('<?php echo $name; ?>');return false;">[deactivate]</a>
				<form class="hidden" id="deactivate_<?php echo $name; ?>" method="post">
					<input type="hidden" name="deactivate_model" value="1" />
				</form>
			<?php } else { ?>
				<form class="hidden" id="activate_<?php echo $name; ?>" method="post">
					<input type="hidden" name="activate_model" value="1" />
				</form>
				<a href="" onclick="activate_model('<?php echo $name; ?>');return false;">[activate]</a>
			<?php } ?>
			<a href="" class="title" onclick="showhide('<?php echo $name; ?>_model');return false;"><?php echo $name; ?></a>
		</h3>
		<div id="<?php echo $name; ?>_model" style="padding:5px">
			<?php echo Models::dlfields($fields, $name, $has); ?>
			<a href="" class="button clear" onclick="new_field('<?php echo $name; ?>');return false;">new field</a>
		</div>
<?php } ?>
