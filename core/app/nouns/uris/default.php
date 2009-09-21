<?php
$uris = $sb->get("uris");
$page = next($this->uri);
empty_nan($page, 0);
$all = $uris->find("*")->GetRows();
$total = $uris->recordCount;
$start_from = $page*25;
$remaining = $total - $start_from;
$shown = ($remaining<25) ? ($remaining % 25) : 25;
$go_to = $start_from + $shown;
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
<?php include("core/app/nouns/settings/nav.php"); ?>
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
<table id="uris_table">
<tr><th>Path</th><th>Template</th><th>Visibility</th><th>Importance</th><th>Owner</th><th>Collective</th><th>Options</th></tr>
<?php for($i=$start_from;$i<$go_to;$i++) { $el = $all[$i]; ?>
	<tr id="uri_<?php echo $el['id']; ?>">
		<td><?php echo $el['path']; ?></td>
		<td><?php echo $el['template']; ?></td>
		<td><?php echo $el['visible']; ?></td>
		<td><?php echo $el['importance']; ?></td>
		<td><?php echo $el['owner']; ?></td>
		<td><?php echo $el['collective']; ?></td>
		<td class="options"><a class="left button" href="#" onclick="edit_uri(<?php echo $el['id']; ?>);return false;">Edit</a>
			<form id="del_form" style="float:left" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[uris]" name="action[uris]" type="hidden" value="delete"/>
				<input type="hidden" name="uris[id]" value="<?php echo $el['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/>
			</form>
		</td>
	</tr>
<?php } ?>
</table>
<a id="add_uri" class="big left button" href="<?php echo uri("uris/create"); ?>" onclick="new_uri();return false;">New Element</a>
