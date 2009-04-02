<p>the following files will be created..</p>
<ul class="file_list">
	<li><?php if (file_exists("app/nouns/".Etc::DEFAULT_TEMPLATE.".php")) echo "<span class=\"right red\">exists</span>"; else echo "<span class=\"right green\">does not exist</span>"; ?>app/nouns/<?php echo Etc::DEFAULT_TEMPLATE; ?>.php</li>
	<li><?php if (file_exists("app/nouns/".Etc::DEFAULT_PATH.".php")) echo "<span class=\"right red\">exists</span>"; else echo "<span class=\"right green\">does not exist</span>"; ?>app/nouns/<?php echo Etc::DEFAULT_PATH; ?>.php</li>
</ul>
<form method="post" action="<?php echo uri("generate"); ?>">
	<input type="hidden" name="generate" value="app"/>
	<div class="field">
		<input type="submit" class="button" value="generate"/>
	</div>
</form>
