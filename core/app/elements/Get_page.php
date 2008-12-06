<?php $page = $this->get_object("Actions")->aget("*", "id='".$_GET['id']."'");$page = $page[0]; ?>
<td><?php echo $page['name']; ?></td>
<td><?php echo $page['template']; ?></td>
<td><?php echo $page['visible']; ?></td>
<td><?php echo $page['importance']; ?></td>
<td><?php echo $page['security']; ?></td>
<td><a class="button" href="#" onclick="javascript:$.get('<?php echo htmlentities('?action=Edit_page&id='.$page['id']); ?>', function(data) {$('#page<?php echo $page['id']; ?>').html(data);});return false;">Edit</a><form id="del_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
<input id="action[Actions]" name="action[Actions]" type="hidden" value="delete"/><input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?')" value="Delete"/></form></td>