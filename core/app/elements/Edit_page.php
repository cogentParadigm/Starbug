<?php
if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
	$page = $this->get_object("Actions")->aget("*", "", "ORDER BY id DESC LIMIT 1"); $page = $page[0]; ?>
<tr id="page<?php echo $page['id']; ?>">
<?php } else {
	$page = $this->get_object("Actions")->aget("*", "id='".$_GET['id']."'"); $page = $page[0]; 
} ?>
	<td><input id="page_name" name="page[name]" value="<?php echo $page['name']; ?>" /></td>
	<td><input id="page_template" name="page[template]" value="<?php echo $page['template']; ?>" /></td>
	<td>
		<select id="visibility" name="page[visible]">
			<option value="0"<?php if ($page['visible']==0) { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($page['visible']==1) { ?> selected="true"<?php } ?>>1</option>
		</select>
	</td>
	<td>
		<select id="importance" name="page[importance]">
			<option value="0"<?php if ($page['importance']==0) { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($page['importance']==1) { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($page['importance']==2) { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($page['importance']==3) { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($page['importance']==4) { ?> selected="true"<?php } ?>>4</option>
			<option value="5"<?php if ($page['importance']==5) { ?> selected="true"<?php } ?>>5</option>
			<option value="6"<?php if ($page['importance']==6) { ?> selected="true"<?php } ?>>6</option>
			<option value="7"<?php if ($page['importance']==7) { ?> selected="true"<?php } ?>>7</option>
			<option value="8"<?php if ($page['importance']==8) { ?> selected="true"<?php } ?>>8</option>
			<option value="9"<?php if ($page['importance']==9) { ?> selected="true"<?php } ?>>9</option>
			<option value="10"<?php if ($page['importance']==10) { ?> selected="true"<?php } ?>>10</option>
		</select>
	</td>
	<td>
		<select id="security" name="page[security]">
			<option value="0"<?php if ($page['security']==0) { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($page['security']==1) { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($page['security']==2) { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($page['security']==3) { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($page['security']==4) { ?> selected="true"<?php } ?>>4</option>
		</select>
	</td>
	<td>
		<a class="button" href="#" onclick="javascript:$.get('<?php echo htmlentities('?action=Get_page&id='.$page['id']); ?>', function(data) {$('#page<?php echo $page['id']; ?>').html(data);});return false;">Cancel</a><a class="button" href="#" onclick="javascript:$.post('<?php echo htmlentities('?action=Get_page&id='.$page['id']); ?>', {'action[Actions]': 'create', 'page[id]': '<?php echo $page['id']; ?>', 'page[name]': $('#page_name').val() }, function(data) {$('#page<?php echo $page['id']; ?>').html(data);});return false;">Save</a>
	</td>
<?php if (empty($_GET['id']) || !is_numeric($_GET['id'])) { ?>
</tr>
<?php } ?>