<?php
$uris = $this->get("uris");
$page = next($this->uri);
empty_nan($page, 0);
$all = $uris->afind("*");
$total = $uris->recordCount;
$list = $uris->afind("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25");
$shown = $uris->recordCount;
?>
<script type="text/javascript">
	function showhide(item) {
		var node = dojo.byId(item);
		var display = node.getAttribute('class');
		if (display == 'hidden') display = '';
		else display = 'hidden';
		node.setAttribute('class', display);
	}
</script>
<?php include("core/public/js/uris.php"); ?>
<h2>URIs</h2>
<?php if ($total > 25) { ?>
<ul class="pages">
	<?php if ($page > 0) { ?>
	<li class="back"><a href="uris/list/<?php echo $page-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($total/25);$i++) { ?>
	<li><a<?php if($page == $i) { ?> class="active"<?php } ?> href="uris/list/<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($page < ceil($total/25)-1) { ?>
	<li class="next"><a href="uris/list/<?php echo $page+1; ?>">Next</a></li>
	<?php } ?>
</ul>
<?php } ?>
<ul id="uris_list" class="lidls">
<?php foreach($list as $entry) { ?>
	<li id ="uri_<?php echo $entry['id']; ?>">
		<h3>
			<form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[uris]" name="action[uris]" type="hidden" value="delete"/>
				<input type="hidden" name="uris[id]" value="<?php echo $el['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="[X]"/>
			</form>
			<a href="uris/update" onclick="edit_uri('<?php echo $entry['id']; ?>');return false;">[edit]</a>
			<a class="title" href="#" onclick="showhide('uri_<?php echo $entry['id']; ?>_list');return false;"><?php echo $entry['path']; ?></a>
		</h3>
		<dl id="uri_<?php echo $entry['id']; ?>_list" style="padding:5px" class="hidden">
			<dt>Template</dt><dd><?php echo $entry['template']; ?></dd>
			<dt>Visible</dt><dd><?php echo $entry['visible']; ?></dd>
			<dt>Importance</dt><dd><?php echo $entry['importance']; ?></dd>
			<dt>Security</dt><dd><?php echo $entry['security']; ?></dd>
		</dl>
	</li>
<?php } ?>
</ul>
<table id="uris_table">
<tr><th>Path</th><th>Template</th><th>Visibility</th><th>Importance</th><th>Security</th><th>Options</th></tr>
<?php foreach($list as $el) { ?>
	<tr id="uri_<?php echo $el['id']; ?>">
		<td><?php echo $el['path']; ?></td>
		<td><?php echo $el['template']; ?></td>
		<td><?php echo $el['visible']; ?></td>
		<td><?php echo $el['importance']; ?></td>
		<td><?php echo $el['security']; ?></td>
		<td class="options"><a class="button" href="#" onclick="edit_uri(<?php echo $el['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[uris]" name="action[uris]" type="hidden" value="delete"/>
				<input type="hidden" name="uris[id]" value="<?php echo $el['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/>
			</form>
		</td>
	</tr>
<?php } ?>
</table>
<a id="add_uri" class="button" href="uris/create" onclick="new_uri();return false;">New Element</a>