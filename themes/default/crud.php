<?php
if (empty($_POST['modelname'])) {
	$infos = array();
	if ($handle = opendir("var/schema/")) {
		while (false !== ($file = readdir($handle))) if ((strpos($file, ".") === false)) $infos[] = $file;
		closedir($handle);
	}
?>
	<form method="post" action="<?php echo uri("generate/crud"); ?>">
		<fieldset>
			<legend>CRUD</legend>
			<div class="field">
				<label for="modelname">Model</label>
				<select style="width:200px" id="modelname" name="modelname">
				<?php foreach ($infos as $name) { ?>
					<option value="<?php echo $name; ?>"<?php if ($_POST['modelname'] == $name) { ?> selected="selected"<?php } ?>><?php echo $name; ?></option>
				<?php } ?>
				</select>
			</div>
			<div class="field">
				<input id="update" type="checkbox" class="left checkbox" name="update" value="true" />
				<label>Files only</label>
			</div>
			<div>
				<input type="submit" class="big button" value="Next"/>
				<a class="big button" href="<?php echo uri("generate"); ?>">Cancel</a>
			</div>
		</fieldset>
	</form>
<?php } else { ?>
	<p>the following path(s) will be added..</p>
	<ul class="file_list">
		<li><?php $rows = $sb->get("uris")->get("*", "path='$_POST[modelname]'")->GetRows(); if (!empty($rows)) echo "<strong class=\"right red\">already exists</strong>"; else echo "<strong class=\"right green\">does not exist</strong>"; ?><?php echo $_POST['modelname']; ?></li>
	</ul>
	<p>the following files will be created..</p>
	<ul class="file_list">
	<?php $newfiles = array("app/nouns/$_POST[modelname]", "app/nouns/$_POST[modelname]/default.php", "app/nouns/$_POST[modelname]/create.php", "app/nouns/$_POST[modelname]/update.php", "app/nouns/$_POST[modelname]/form.php"); foreach($newfiles as $newfile) { ?>
		<li><?php if (file_exists($newfile)) echo "<strong class=\"right red\">already exists</strong>"; else echo "<strong class=\"right green\">does not exist</strong>"; ?><?php echo $newfile; ?></li>
	<?php } ?>
	</ul>
	<br><br>
	<form method="post" action="<?php echo uri("generate"); ?>">
		<fieldset>
			<legend>CRUD</legend>
			<input type="hidden" name="generate" value="crud"/>
			<?php if ($_POST['update']) { ?><input type="hidden" name="update" value="true"/><?php } ?>
			<input type="hidden" name="modelname" value="<?php echo $_POST['modelname']; ?>"/>
			<div>
				<input type="submit" class="big button" value="Generate"/>
				<a class="big button" href="<?php echo uri("generate"); ?>">Cancel</a>
			</div>
		</fieldset>
	</form>
<?php } ?>
