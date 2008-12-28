<?php
$elements = $this->get_object("Elements");
$page = next($this->uri);
empty_nan($page, 0);
$all = $elements->afind("*");
$total = $elements->recordCount;
$list = $elements->afind("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25");
$shown = $elements->recordCount;
?>
<script type="text/javascript">
	function new_element() {
		dojo.xhrGet({
			url: 'elements/new',
			load: function (data) {
				dojo.byId('elements_table').innerHTML .= data;
			}
		}
	}
	function edit_element(id) {
		dojo.xhrGet({
			url: 'elements/edit',
			load: function(data) {
				dojo.byId('element_'+id).innerHTML = data;
			}
		}
	}
	function submit_element() {
		
	}
</script>
<h2>Pages</h2>
<?php if ($total > 25) { ?>
<ul class="pages">
	<?php if ($page > 0) { ?>
	<li class="back"><a href="elements/list/<?php echo $page-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($total/25);$i++) { ?>
	<li><a<?php if($page == $i) { ?> class="active"<?php } ?> href="elements/list/<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($page < ceil($total/25)-1) { ?>
	<li class="next"><a href="elements/list<?php echo $page+1; ?>">Next</a></li>
	<?php } ?>
</ul>
<?php } ?>
<table id="elements_table">
<tr><th>Name</th><th>Template</th><th>Visibility</th><th>Importance</th><th>Security</th><th>Options</th></tr>
<?php foreach($list as $el) { ?>
	<tr id="element_<?php echo $el['id']; ?>">
		<td><?php echo $el['name']; ?></td>
		<td><?php echo $el['template']; ?></td>
		<td><?php echo $el['visible']; ?></td>
		<td><?php echo $el['importance']; ?></td>
		<td><?php echo $el['security']; ?></td>
		<td><a class="button" href="#" onclick="edit_element(<?php echo $el['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[Actions]" name="action[Actions]" type="hidden" value="delete"/>
				<input type="hidden" name="page[id]" value="<?php echo $el['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/>
			</form>
		</td>
	</tr>
<?php } ?>
</table>
<a id="addpage" class="button" href="elements/create" onclick="new_element();return false;">Add Page</a>