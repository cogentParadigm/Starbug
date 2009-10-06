<?php
	include("core/app/models/Models.php");
	$models = new Models($sb->db);
	if (!empty($_POST['add_key'])) {
		$models->add_key($_POST['key'], $_POST['value'], $_POST['add_key']); ?>
	<dt><?php echo $_POST['key']; ?></dt><dd id="<?php echo $_POST['add_key']."-".$_POST['key']; ?>"><span class="options"><a href="">edit</a><a href="">delete</a></span><?php echo $_POST['value']; ?></dd>
<?php } else {
	$loc = next($this->uri);
	if ($_POST['edit_key']) $models->edit($_POST['value'], $loc);
	$val = $models->get($loc);
?>
<span class="options">
<a href="" class="edit_key">edit</a><a href="">delete</a></span><?php echo $val; ?>
</span>
<?php } ?>
