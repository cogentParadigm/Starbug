<?php
$permits = $this->get("permits");
$page = next($this->uri);
empty_nan($page, 0);
$all = $permits->afind("*");
$total = $permits->recordCount;
$list = $permits->afind("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25");
$shown = $permits->recordCount;
?>
<h2>Permits</h2>
<?php include("core/app/nouns/settings/nav.php"); ?>
<?php if ($total > 25) { ?>
<ul class="pages">
	<?php if ($page > 0) { ?>
	<li class="back"><a href="permits/list/<?php echo $page-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($total/25);$i++) { ?>
	<li><a<?php if($page == $i) { ?> class="active"<?php } ?> href="permits/list/<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($page < ceil($total/25)-1) { ?>
	<li class="next"><a href="permits/list/<?php echo $page+1; ?>">Next</a></li>
	<?php } ?>
	</ul>
<?php } ?>
<table id="permits_table">
	<tr>
		<th>Role</th><th>Who</th><th>Action</th><th>Status</th><th>Priv Type</th><th>Related Table</th><th>Related ID</th>
	</tr>
<?php foreach($list as $entry) { ?>
	<tr id ="permits_<?php echo $entry['id']; ?>">
		<td><?php echo $entry['role']; ?></td>
		<td><?php echo $entry['who']; ?></td>
		<td><?php echo $entry['action']; ?></td>
		<td><?php echo $entry['status']; ?></td>
		<td><?php echo $entry['priv_type']; ?></td>
		<td><?php echo $entry['related_table']; ?></td>
		<td><?php echo $entry['related_id']; ?></td>
		<td>
			<a class="left button" href="<?php echo uri("permits/update/$entry[id]"); ?>">edit</a>
			<form style="float:left" id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[permits]" name="action[permits]" type="hidden" value="delete"/>
				<input type="hidden" name="permits[id]" value="<?php echo $entry['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="delete"/>
			</form>
		</td>
	</tr>
<?php } ?>
</table>
<a id="add_permits" class="big button" href="<?php echo uri("permits/create"); ?>">new permit</a>
