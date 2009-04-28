<?php
if (empty($_POST['modelname'])) {
	$infos = array();
	if ($handle = opendir("core/db/schema/")) {
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
		<li><?php if (file_exists("app/models/".ucwords($_POST['modelname']).".php")) echo "<span class=\"right red\">exists</span>"; else echo "<span class=\"right green\">does not exist</span>"; ?>app/models/<?php echo ucwords($_POST['modelname']); ?>.php</li>
	</ul>
	<form method="post" action="<?php echo uri("generate"); ?>">
		<input type="hidden" name="generate" value="model"/>
		<input type="hidden" name="modelname" value="<?php echo $_POST['modelname']; ?>"/>
		<div>
			<input type="submit" class="big button" value="Generate"/>
			<a class="big button" href="<?php echo uri("generate"); ?>">Cancel</a>
		</div>
	</form>
<?php } ?>
