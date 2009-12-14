<script type="text/javascript">
	function switch_icon(text) {
		if (text == '--') return '&crarr;';
		else return '--';
	}
</script>
<h2>URIs</h2>
<a id="add_uri" class="right round button" href="<?php echo uri("sb/uris/create"); ?>">Create URI</a>
<?php include("core/app/nouns/sb/settings/nav.php"); ?>
<table id="uris_table" class="clear lister">
<?php foreach(array("thead", "tfoot") as $t) { ?><?php echo "<$t>"; ?><tr><th class="expand-col"></th><th class="path-col">Path</th><th class="parent-col">Parent</th><th class="owner-col">Owner</th><th class="collective-col">Collective</th></tr><?php echo "</$t>"; ?><?php } ?>
<?php
$sb->import("util/dojo");
$uris = $sb->query("uris", "action:read");
$kids = array();
foreach($uris as $uri) $kids[$uri['parent']][] = $uri;
function list_uri($row, $kids) { global $sb; global $request; global $dojo; ?>
	<tr id="uris_<?php echo $row['id']; ?>">
		<td class="expand-col"><?php if (!empty($kids[$row['id']])) echo '&crarr;'; ?></td>
		<td class="path-col">
			<a href="<?php echo uri("sb/uris/update/$row[id]"); ?>"><?php echo $row['path']; ?></a>
			<ul class="row-actions">
				<li class="first"><a href="<?php echo uri("sb/uris/update/$row[id]"); ?>">edit</a></li>
				<li><a href="<?php echo uri($request->path."?action=delete&id=$row[id]"); ?>">delete</a></li>
				<li><a href="<?php echo uri($row['path']); ?>">view</a></li>
			</ul>
		</td>
		<td class="parent-col"><?php $parent = $sb->query("uris", "select:path	where:id='$row[parent]'	limit:1"); echo $parent['path']; ?></td>
		<td class="owner-col"><?php $owner = $sb->query("users", "select:email	where:id='$row[owner]'	limit:1"); echo $owner['email']; ?></td>
		<td class="owner-col"><?php echo array_search($row['collective'], array_merge(array("everybody" => 0), $request->groups)); ?></td>
	</tr>
	<?php
		if (!empty($kids[$row['id']])) { ?>
	<tr id="parent_<?php echo $row['id']; ?>" style="display:none">
		<td colspan="5">
		<table class="lister">
		<?php
			$dojo->toggle("#uris_".$row['id']." .expand-col", "tg_".$row['id'], "parent_".$row['id'], "default:off	add:showFunc:dojo.fx.wipeIn, hideFunc:dojo.fx.wipeOut, duration:300");
			$dojo->attach("#uris_".$row['id']." .expand-col", "sb.replace", "node:evt.target	data:switch_icon(evt.target.innerHTML)");
			foreach($kids[$row['id']] as $uri) list_uri($uri, $kids); 
		?>
		</table>
		</td>
	</tr>
	<?php
		}
	?>
<?php } ?>
<?php foreach($kids[0] as $uri) list_uri($uri, $kids); ?>
</table>

<a id="add_uri" class="big left round button" href="<?php echo uri("sb/uris/create"); ?>">Create URI</a>
