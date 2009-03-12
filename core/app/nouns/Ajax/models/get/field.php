<?php
	include("core/app/models/Models.php");
	$models = new Models($this->db);
	if (!empty($_POST['new_field'])) {
		$models->add_field($_POST['fieldname'], $_POST['new_field']);
?>
<dt id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-key" class="sub">
	<a href="" class="right" onclick="if (confirm('Are you sure you want to delete?')) {delete_key('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return false;}">delete</a>
	<a href="" class="right" onclick="edit_field('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return fals;">rename</a>
	<a href="" class="right" onclick="new_key('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return false;">add key</a>
	<a href="" onclick="showhide('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-fields');return false;"><?php echo $_POST['fieldname']; ?></a>
</dt>
<dd id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>">
	<dl id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-fields">
	</dl>
</dd>
<?php } else {
	$loc = next($this->uri);
	if ($_POST['edit_field']) $models->edit($_POST['key'], $loc);
	$keys = end(split("-", $loc, 2));
	$k = end(split("-", $keys));
?>
	<a href="" class="right" onclick="if (confirm('Are you sure you want to delete?')) {delete_key('<?php echo $keys; ?>');return false;}">delete</a>
	<a href="" class="right" onclick="edit_field('<?php echo $keys; ?>');return false;">rename</a>
	<a href="" class="right" onclick="new_key('<?php echo $keys; ?>');return false;">add key</a>
	<a href="" onclick="showhide('<?php echo $keys; ?>-fields');return false;"><?php echo $k; ?></a>
<?php } ?>
