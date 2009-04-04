<li id="new_model" class="inactive">
	<h3>
		<form id="new_model_form" method="post" style="float:left;margin-left:5px">
			<input name="new_model" type="hidden" value="1" />
			<div style="padding:5px 0px">
				<input id="modelname" name="modelname" type="text"<?php if (!empty($_POST['modelname'])) { ?> value="<?php echo $_POST['modelname']; ?>"<?php } ?> />
			</div>
		</form>
		<a href="" onclick="cancel_new_model();return false;">cancel</a>
		<a href="" onclick="save_new_model();return false;">save</a>
	</h3>
</li>