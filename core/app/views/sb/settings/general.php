<?php
if ($_POST['save_settings']) {
	$etc = file_get_contents("etc/Etc.php");
	foreach ($_POST['etc'] as $k => $v) {
		$k = strtoupper($k);
		$etc = preg_replace("/const $k = \"([^\"]*)\";/", "const $k = \"$v\";", $etc);
	}
	$file = fopen("etc/Etc.php", "wb");
	fwrite($file, $etc);
	fclose($file);
	header("Location: ".uri("sb/settings/general"));
}
$sb->import("util/form");
$f = new form("etc");
?>
<h2>Settings</h2>
<?php include("core/app/views/sb/settings/nav.php"); ?>
<?php echo $f->open('id="settings_form"'); ?>
	<?php echo $f->hidden("save_settings	value:true"); ?>
	<fieldset>
		<legend>Website info</legend>
		<div class="field">
			<?php echo $f->text("website_name	default:".Etc::WEBSITE_NAME); ?>
		</div>
		<div class="field">
			<?php echo $f->text("website_url	label:Website URL	default:".Etc::WEBSITE_URL); ?>
		</div>
		<div class="field">
			<?php echo $f->text("tagline	label:Tagline or Description	default:".Etc::TAGLINE); ?>
		</div>
	</fieldset>
	<fieldset>
		<legend>Email addresses</legend>
		<div class="field">
			<?php echo $f->text("webmaster_email	default:".Etc::WEBMASTER_EMAIL); ?>
		</div>
		<div class="field">
			<?php echo $f->text("contact_email	default:".Etc::CONTACT_EMAIL); ?>
		</div>
	</fieldset>
	<div><?php echo $f->submit("class:big round button	value:Save"); ?></div>
</form>
