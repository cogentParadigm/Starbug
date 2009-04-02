<?php
	$id = next($this->uri);
	if (!empty($this->errors['uris'])) include("core/app/nouns/Ajax/uris/".(($id)?"edit":"new").".php");
	else {
		$new = false;
		if (!$id) { $id = $this->db->Insert_ID(); $new = true; }
		$uri = $this->get("uris")->find("*", "id='".$id."'")->fields();
		if ($new) { ?><tr id="uri_<?php echo $id; ?>"><?php } ?>
		<td><?php echo $uri['path']; ?></td>
		<td><?php echo $uri['template']; ?></td>
		<td><?php echo $uri['visible']; ?></td>
		<td><?php echo $uri['importance']; ?></td>
		<td><?php echo $uri['owner']; ?></td>
		<td><?php echo $uri['collective']; ?></td>
		<td>
			<a class="button" href="#" onclick="edit_uri(<?php echo $uri['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input name="action[uris]" type="hidden" value="delete"/>
				<input type="hidden" name="uris[id]" value="<?php echo $uri['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?')" value="Delete"/>
			</form>
		</td>
<?php if ($new) echo "</tr>"; } ?>
