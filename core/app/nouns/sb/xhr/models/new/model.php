<li id="new_model" class="inactive">
	<h3>
		<form id="new_model_form" method="post" style="float:left;margin-left:5px">
			<input name="new_model" type="hidden" value="1" />
			<div style="padding:5px 0px">
				<input id="modelname" name="modelname" type="text"<?php if (!empty($_POST['modelname'])) { ?> value="<?php echo $_POST['modelname']; ?>"<?php } ?> />
			</div>
		</form>
		<a href="" class="cancel_new_model">cancel</a>
		<a href="" class="save_new_model">save</a>
	</h3>
</li>
