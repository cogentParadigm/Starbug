<?php $element = $this->get_object("Elements")->aget("*", "id='".$_GET['id']."'");$element = $element[0]; ?>
<td><?php echo $element['name']; ?></td>
<td><?php echo $element['template']; ?></td>
<td><?php echo $element['visible']; ?></td>
<td><?php echo $element['importance']; ?></td>
<td><?php echo $element['security']; ?></td>
<td>
	<a class="button" href="#" onclick="edit_page(<?php echo $element['id']; ?>);return false;">Edit</a>
	<form id="del_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
		<input name="action[Elements]" type="hidden" value="delete"/>
		<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?')" value="Delete"/>
	</form>
</td>