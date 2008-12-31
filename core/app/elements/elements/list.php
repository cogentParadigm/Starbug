<?php
$elements = $this->get("elements");
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
			url: '<?php echo uri("elements/new"); ?>',
			load: function (data) {
				dojo.byId('elements_table').innerHTML += data;
			}
		});
	}
	function save_new() {
		dojo.xhrPost({
			url: '<?php echo uri("elements/get"); ?>',
			form: 'new_element_form',
			load: function(data) {
				cancel_new();
				dojo.byId('elements_table').innerHTML += data;
			}
		});
	}
	function cancel_new() {
		var newrow = dojo.byId('new_element');
		newrow.parentNode.removeChild(newrow);
	}
	function edit_element(id) {
		dojo.xhrGet({
			url: '<?php echo uri("elements/edit/"); ?>'+id,
			load: function(data) {
				dojo.byId('element_'+id).innerHTML = data;
			}
		});
	}
	function save_edit(id) {
		dojo.xhrPost({
			url: '<?php echo uri("elements/get/"); ?>'+id,
			form: 'edit_element_form',
			load: function(data) {
				dojo.byId('element_'+id).innerHTML = data;
			}
		});
	}
	function cancel_edit(id) {
		dojo.xhrGet({
			url: '<?php echo uri("elements/get/"); ?>'+id,
			load: function(data) {
				dojo.byId('element_'+id).innerHTML = data;
			}
		});
	}
</script>
<h2>Elements</h2>
<?php if ($total > 25) { ?>
<ul class="pages">
	<?php if ($page > 0) { ?>
	<li class="back"><a href="elements/list/<?php echo $page-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($total/25);$i++) { ?>
	<li><a<?php if($page == $i) { ?> class="active"<?php } ?> href="elements/list/<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($page < ceil($total/25)-1) { ?>
	<li class="next"><a href="elements/list/<?php echo $page+1; ?>">Next</a></li>
	<?php } ?>
</ul>
<?php } ?>
<table id="elements_table">
<tr><th>Path</th><th>Template</th><th>Visibility</th><th>Importance</th><th>Security</th><th>Options</th></tr>
<?php foreach($list as $el) { ?>
	<tr id="element_<?php echo $el['id']; ?>">
		<td><?php echo $el['path']; ?></td>
		<td><?php echo $el['template']; ?></td>
		<td><?php echo $el['visible']; ?></td>
		<td><?php echo $el['importance']; ?></td>
		<td><?php echo $el['security']; ?></td>
		<td class="options"><a class="button" href="#" onclick="edit_element(<?php echo $el['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[Elements]" name="action[Elements]" type="hidden" value="delete"/>
				<input type="hidden" name="elements[id]" value="<?php echo $el['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/>
			</form>
		</td>
	</tr>
<?php } ?>
</table>
<a id="add_element" class="button" href="elements/create" onclick="new_element();return false;">New Element</a>