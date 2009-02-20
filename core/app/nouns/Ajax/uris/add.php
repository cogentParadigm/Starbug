<div id="add_uri" class="box">
		<?php if (($_POST['action']['uris'] == "create") && empty($this->errors['uris'])) { ?>
		<p><?php echo $_POST['uris']['path']; ?> has been added.</p>
				<ul class="buttons">
			<li><a class="button" href="#" onclick="cancel_add();return false;">Close</a></li>
		</ul>
		<?php } else { ?>
		<?php $formid = "add_uri_form"; $action = "create"; include("core/app/nouns/uris/uris_form.php"); ?>
		<ul class="buttons">
			<li><a class="button" href="#" onclick="save_add();return false;">Save</a></li>
			<li><a class="button" href="#" onclick="cancel_add();return false;">Cancel</a></li>
		</ul>
		<?php } ?>
</div>