<p>the following path(s) will be added..</p>
<ul class="file_list">
	<li><?php $rows = $sb->get("uris")->get("*", "path='".Etc::DEFAULT_PATH."'")->GetRows(); if (!empty($rows)) echo "<strong class=\"right red\">already exists</strong>"; else echo "<strong class=\"right green\">does not exist</strong>"; ?><?php echo Etc::DEFAULT_PATH; ?></li>
</ul>
<p>the following files will be created..</p>
<ul class="file_list">
<?php $newfiles = array("app/nouns/".Etc::DEFAULT_TEMPLATE.".php", "app/nouns/".Etc::DEFAULT_PATH.".php", "app/nouns/header.php", "app/nouns/footer.php", "app/nouns/missing.php"); foreach($newfiles as $newfile) { ?>
	<li><?php if (file_exists($newfile)) echo "<strong class=\"right red\">already exists</strong>"; else echo "<strong class=\"right green\">does not exist</strong>"; ?><?php echo $newfile; ?></li>
<?php } ?>
</ul>
<br><br>
<form method="post" action="<?php echo uri("sb/generate"); ?>">
	<fieldset>
		<legend>Bare Application</legend>
		<input type="hidden" name="generate" value="app"/>
		<div class="field">
			<input type="submit" class="big button" value="Generate"/>
			<a class="big button" href="<?php echo uri("sb/generate"); ?>">Cancel</a>
		</div>
	</fieldset>
</form>
