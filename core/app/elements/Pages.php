<?php
$pages = $this->get_object("Actions");
if (!isset($_GET["page"]) || !is_numeric($_GET["page"])) $_GET["page"] = 0;
$allpages = $pages->afind("*");
$totalpages = $pages->recordCount;
$pagelist = $pages->afind("*", "", "ORDER BY id DESC LIMIT ".($_GET['page']*25).", 25");
$currentpages = $pages->recordCount;
?>
<h2>Pages</h2>
<?php if ($totalpages > 25) { ?>
<ul class="pages">
	<?php if ($_GET['page'] > 0) { ?>
	<li class="back"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;page=<?php echo $_GET["page"]-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($totalpages/25);$i++) { ?>
	<li><a<?php if($_GET["page"] == $i) { ?> class="active"<?php } ?> href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;page=<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($_GET["page"] < ceil($totalpages/25)-1) { ?>
	<li class="next"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;page=<?php echo $_GET["page"]+1; ?>">Next</a></li>
	<?php } ?>
</ul>
<?php } ?>
<table id="pages_table">
<tr><th>Name</th><th>Template</th><th>Visibility</th><th>Importance</th><th>Security</th><th>Options</th></tr>
<?php foreach($pagelist as $page) { ?>
	<tr id="page<?php echo $page['id']; ?>">
		<td><?php echo $page['name']; ?></td>
		<td><?php echo $page['template']; ?></td>
		<td><?php echo $page['visible']; ?></td>
		<td><?php echo $page['importance']; ?></td>
		<td><?php echo $page['security']; ?></td>
		<td><a class="button" href="#" onclick="javascript:$.get('<?php echo htmlentities('?action=Edit_page&id='.$page['id']); ?>', function(data) {$('#page<?php echo $page['id']; ?>').html(data);});return false;">Edit</a><form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
			<input id="action[Actions]" name="action[Actions]" type="hidden" value="delete"/><input type="hidden" name="page[id]" value="<?php echo $page['id']; ?>"/><input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/></form></td>
	</tr>
<?php } ?>
</table>
<a id="addpage" class="button" href="#" onclick="javascript:$.post('<?php echo htmlentities('?action=Edit_page'); ?>', {'action[Actions]': 'create', 'page[name]': 'Page Name'}, function(data) {$('#pages_table').append(data);});return false;">Add Page</a>