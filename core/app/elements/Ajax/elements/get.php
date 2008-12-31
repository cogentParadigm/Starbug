<?php
	$id = next($this->uri);
	if (!empty($this->errors['elements'])) include("core/app/elements/Ajax/elements/".(($id)?"edit":"new").".php");
	else {
		$new = false;
		if (!$id) { $id = $this->db->Insert_ID(); $new = true; }
		$element = $this->get("elements")->find("*", "id='".$id."'")->fields();
		if ($new) { ?><tr id="element_<?php echo $id; ?>"><?php } ?>
		<td><?php echo $element['path']; ?></td>
		<td><?php echo $element['template']; ?></td>
		<td><?php echo $element['visible']; ?></td>
		<td><?php echo $element['importance']; ?></td>
		<td><?php echo $element['security']; ?></td>
		<td>
			<a class="button" href="#" onclick="edit_element(<?php echo $element['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input name="action[elements]" type="hidden" value="delete"/>
				<input type="hidden" name="elements[id]" value="<?php echo $element['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?')" value="Delete"/>
			</form>
		</td>
<?php if ($new) echo "</tr>"; } ?>