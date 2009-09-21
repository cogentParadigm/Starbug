<?php
if (empty($_POST['modelname'])) {
	$infos = array();
	if ($handle = opendir("var/schema/")) {
		while (false !== ($file = readdir($handle))) if ((strpos($file, ".") === false)) $infos[] = $file;
		closedir($handle);
	}
?>
	<form method="post" action="<?php echo uri("generate/model"); ?>">
		<fieldset>
			<legend>Database Model</legend>
			<div class="field">
				<label for="modelname">Model</label>
				<select style="width:200px" id="modelname" name="modelname">
				<?php foreach ($infos as $name) { ?>
					<option value="<?php echo $name; ?>"<?php if ($_POST['modelname'] == $name) { ?> selected="selected"<?php } ?>><?php echo $name; ?></option>
				<?php } ?>
				</select>
			</div>
			<div>
				<input type="submit" class="big button" value="Next"/>
				<a class="big button" href="<?php echo uri("generate"); ?>">Cancel</a>
			</div>
		</fieldset>
	</form>
<?php } else { ?>
	<p>the following files will be created..</p>
	<ul class="file_list">
	<?php $newfiles = array("app/models/".ucwords($_POST['modelname']).".php"); foreach($newfiles as $newfile) { ?>
		<li><?php if (file_exists($newfile)) echo "<strong class=\"right red\">already exists</strong>"; else echo "<strong class=\"right green\">does not exist</strong>"; ?><?php echo $newfile; ?></li>
	<?php } ?>
	</ul>
	<br><br>
	<form method="post" action="<?php echo uri("generate"); ?>">
		<input type="hidden" name="generate" value="model"/>
		<input type="hidden" name="modelname" value="<?php echo $_POST['modelname']; ?>"/>
		<div>
			<input type="submit" class="big button" value="Generate"/>
			<a class="big button" href="<?php echo uri("generate"); ?>">Cancel</a>
		</div>
	</form>
<?php } ?>
