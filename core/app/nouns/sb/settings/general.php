<?php
if ($_POST['save_settings']) {
	$etc = file_get_contents("etc/Etc.php");
	foreach ($_POST as $k => $v) {
		$k = strtoupper($k);
		$etc = preg_replace("/const $k = \"([^\"]*)\";/", "const $k = \"$v\";", $etc);
	}
	$file = fopen("etc/Etc.php", "wb");
	fwrite($file, $etc);
	fclose($file);
	header("Location: ".uri("sb/settings/general"));
}
?>
<h2>Settings</h2>
<?php include("core/app/nouns/sb/settings/nav.php"); ?>
<form id="settings_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="save_settings" value="true"/>
	<fieldset>
		<legend>Website info</legend>
		<div class="field">
			<label for="website_name">Website name</label>
			<input id="website_name" name="website_name" type="text" class="text" value="<?php echo Etc::WEBSITE_NAME; ?>"/>
		</div>
		<div class="field">
			<label for="website_url">Website URL</label>
			<input id="website_url" name="website_url" type="text" class="text" value="<?php echo Etc::WEBSITE_URL; ?>"/>
		</div>
		<div class="field">
			<label for="prefix">Prefix</label>
			<input id="prefix" name="prefix" type="text" class="text" value="<?php echo Etc::PREFIX; ?>"/>
		</div>
	</fieldset>
	<fieldset>
		<legend>Email addresses</legend>
		<div class="field">
			<label for="webmaster_email">Webmaster email</label>
			<input id="webmaster_email" name="webmaster_email" type="text" class="text" value="<?php echo Etc::WEBMASTER_EMAIL; ?>"/>
		</div>
		<div class="field">
			<label for="contact_email">Contact email</label>
			<input id="contact_email" name="contact_email" type="text" class="text" value="<?php echo Etc::CONTACT_EMAIL; ?>"/>
		</div>
	</fieldset>
	<div><input class="big button" type="submit" value="Save"/></div>
</form>
