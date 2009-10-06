<?php
if (!empty($this->errors['page'])) include("core/app/nouns/sb/pages/".(($_POST['pages']['id'])?"update":"create").".php");
else {
	$sb->import("util/pager");
	$pager = new pager($sb->query("pages"), $sb->recordCount, 25, next($this->uri));
	?>
	<h2><a class="right button" href="<?php echo uri("sb/pages/create"); ?>">Create Page</a>Pages</h2>
	<?php $pager->links("sb/pages/"); ?>
	<table class="clear lister">
	<?php foreach(array("thead", "tfoot") as $t) { ?><?php echo "<$t>"; ?><tr><th class="title-col">Title</th><th class="date-col">Last Modified</th><th class="status-col">Status</th></tr><?php echo "</$t>"; ?><?php } ?>
	<?php while($row = $pager->item()) { ?>
		<tr id ="page_<?php echo $row['id']; ?>">
			<td class="title-col">
				<a href="<?php echo uri("sb/pages/update/$row[id]"); ?>"><?php echo $row['title']; ?></a>
				<ul class="row-actions">
					<li class="first"><a href="<?php echo uri("sb/pages/update/$row[id]"); ?>">edit</a></li>
					<li><a href="<?php echo uri($this->path."?action=delete&id=$row[id]"); ?>">delete</a></li>
					<li><a href="<?php echo uri($row['name']); ?>">view</a></li>
				</ul>
			</td>
			<td class="date-col"><?php echo date("F jS, Y", strtotime($row['modified'])); ?></td>
			<td class="status-col"><?php echo array_search($row['status'], $this->statuses); ?></td>
		</tr>
	<?php } ?>
	</table>
<?php } ?>
