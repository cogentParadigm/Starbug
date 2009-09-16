<form<?php if (!empty($formid)) echo " id=\"$formid\""; ?> class="permits_form" action="<?php echo (empty($submit_to) ? $_SERVER['REQUEST_URI'] : $submit_to); ?>" method="post">
	<input class="action" name="action[permits]" type="hidden" value="<?php echo $action; ?>" />
	<?php if (!empty($_POST['permits']['id'])) { ?><input id="id" name="permits[id]" type="hidden" value="<?php echo $_POST['permits']['id']; ?>" /><?php } ?>
	<div class="field">
		<label for="role">Role</label>
		<?php if (!empty($sb->errors['permits']['roleError'])) { ?><span class="error">Please enter a Role</span><?php } ?>
		<select id="role" name="permits[role]" onChange="who_options()">
		<option value="everyone"<?php if ($_POST["permits"]["role"] == "everyone") { ?> select="selected"<?php } ?>>everyone</option>
			<option value="user"<?php if ($_POST["permits"]["role"] == "users") { ?> select="selected"<?php } ?>>user</option>
			<option value="group"<?php if ($_POST["permits"]["role"] == "group") { ?> select="selected"<?php } ?>>group</option>
			<option value="owner"<?php if ($_POST["permits"]["role"] == "owner") { ?> select="selected"<?php } ?>>owner</option>
			<option value="collective"<?php if ($_POST["permits"]["role"] == "collective") { ?> select="selected"<?php } ?>>collective</option>
		</select>
	</div>
	<div class="field">
		<script type="text/javascript">
			function who_options() {
				var role = dojo.byId("role");
				var who = dojo.byId("who");
				if (role.selectedIndex == '1') {
					dojo.xhrGet({
						url: '<?php echo uri("ajax/permits/who/user"); ?>',
						load: function (data) {
							who.innerHTML = data;
						}
					});
				} else if (role.selectedIndex == '2') {
					dojo.xhrGet({
						url: '<?php echo uri("ajax/permits/who/group"); ?>',
						load: function (data) {
							who.innerHTML = data;
						}
					});
				} else {
					who.innerHTML = '<option value="0" selected="selected">nobody or n/a</option>';
				}
				
			}
		</script>
		<label for="who">Who</label>
		<?php dfault($_POST['permits']['who'], "0"); $users ?>
		<select id="who" name="permits[who]">
			<option value="0"<?php if ($_POST["permits"]["who"] == "0") { ?> select="selected"<?php } ?>>nobody or n/a</option>
			<option value="1"<?php if ($_POST["permits"]["role"] == "1") { ?> select="selected"<?php } ?>>deleted</option>
			<option value="2"<?php if ($_POST["permits"]["role"] == "2") { ?> select="selected"<?php } ?>>inactive</option>
			<option value="4"<?php if ($_POST["permits"]["role"] == "4") { ?> select="selected"<?php } ?>>active</option>
			<option value="8"<?php if ($_POST["permits"]["role"] == "8") { ?> select="selected"<?php } ?>>cancelled</option>
			<option value="16"<?php if ($_POST["permits"]["role"] == "16") { ?> select="selected"<?php } ?>>pending</option>
		</select>
	</div>
	<div class="field">
		<label for="action">Action</label>
		<?php if (!empty($sb->errors['permits']['actionError'])) { ?><span class="error">Please enter a Action</span><?php } ?>
		<input id="action" name="permits[action]" type="text" style="width:100px" <?php if (!empty($_POST['permits']['action'])) { ?> value="<?php echo $_POST['permits']['permits']; ?>"<?php } ?>/>
	</div>
	<div class="field">
		<label for="status">Status</label>
		<?php dfault($_POST['permits']['status'], "31"); ?>
		<select id="status" name="permits[status]">
			<option value="31"<?php if ($_POST["permits"]["role"] == "31") { ?> select="selected"<?php } ?>>any</option>
			<option value="1"<?php if ($_POST["permits"]["role"] == "1") { ?> select="selected"<?php } ?>>deleted</option>
			<option value="2"<?php if ($_POST["permits"]["role"] == "2") { ?> select="selected"<?php } ?>>inactive</option>
			<option value="4"<?php if ($_POST["permits"]["role"] == "4") { ?> select="selected"<?php } ?>>active</option>
			<option value="8"<?php if ($_POST["permits"]["role"] == "8") { ?> select="selected"<?php } ?>>cancelled</option>
			<option value="16"<?php if ($_POST["permits"]["role"] == "16") { ?> select="selected"<?php } ?>>pending</option>
		</select>
	</div>
	<div class="field">
		<label for="priv_type">Priv type</label>
		<?php dfault($_POST['permits']['priv_type'], "table"); ?>
		<select id="priv_type" name="permits[priv_type]">
			<option value="table"<?php if ($_POST["permits"]["role"] == "table") { ?> select="selected"<?php } ?>>table</option>
			<option value="object"<?php if ($_POST["permits"]["role"] == "object") { ?> select="selected"<?php } ?>>object</option>
			<option value="global"<?php if ($_POST["permits"]["role"] == "global") { ?> select="selected"<?php } ?>>global</option>
		</select>
	</div>
	<div class="field">
		<label for="related_table">Related table</label>
		<?php if (!empty($sb->errors['permits']['related_tableError'])) { ?><span class="error">Please enter a Related table</span><?php } ?>
		<select id="related_table" name="permits[related_table]" onchange="id_options()">
			<option value="gpl_uris"<?php if ($_POST["permits"]["role"] == "gpl_uris") { ?> select="selected"<?php } ?>>uris</option>
			<option value="gpl_users"<?php if ($_POST["permits"]["role"] == "gpl_users") { ?> select="selected"<?php } ?>>users</option>
		</select>
	</div>
	<div class="field">
		<script type="text/javascript">
			function id_options() {
				var rel_table = dojo.byId("related_table");
				var rel_id = dojo.byId("related_id");
				var token = false;
				if (rel_table.selectedIndex == '0') token = 'uris';
				else if (rel_table.selectedIndex == '1') token = 'users';
				if (token == false) who.innerHTML = '<option value="0" selected="selected">nobody or n/a</option>';
				else {
					dojo.xhrGet({
						url: '<?php echo uri("ajax/permits/relid/"); ?>'+token,
						load: function (data) {
							rel_id.innerHTML = data;
						}
					});
				}
			}
		</script>
		<label for="related_id">Related id</label>
		<?php if (!empty($sb->errors['permits']['related_idError'])) { ?><span class="error">Please enter a Related id</span><?php } ?>
		<?php dfault($_POST['permits']['related_id'], 0); ?>
		<select id="related_id" name="permits[related_id]">
			<option value="0">nobody</option>
		</select>
	</div>
	<div class="clear"><input class="button" type="submit" value="create permit" /></div>
</form>
