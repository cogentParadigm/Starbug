<?php
if ($_POST['save_labels']) {
	foreach ($_POST as $k => $v) {
		if (file_exists("core/db/schema/$k")) {
			$info = (file_exists("core/db/schema/.info/$k")) ? unserialize(file_get_contents("core/db/schema/.info/$k")) : array();
			$info["label"] = $v;
			$file = fopen("core/db/schema/.info/$k", "wb");
			fwrite($file, serialize($info));
			fclose($file);
		}
	}
}
$infos = array();
if ($handle = opendir("core/db/schema/")) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") === false)) $infos[$file] = unserialize(file_get_contents("core/db/schema/.info/".$file));
	closedir($handle);
}
?>
<h2>Settings</h2>
<?php include("core/app/nouns/settings/nav.php"); ?>
<form id="settings_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="save_labels" value="true"/>
	<fieldset>
	<?php foreach ($infos as $name => $info) { if (!isset($info['label'])) $info['label'] = ""; ?>
		<legend><?php echo $name; ?></legend>
		<div class="field">
			<input id="<?php echo $name; ?>" name="<?php echo $name; ?>" type="text" class="text" value="<?php echo $info['label']; ?>"/>
		</div>
	<?php } ?>
	</fieldset>
	<input class="big button" type="submit" value="Save"/>
</form>
