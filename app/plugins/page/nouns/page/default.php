<?php
$pages = $this->get("page");
$all = $pages->get("*");
$total = $pages->recordCount;
if (!empty($this->errors['page'])) {
	$id = next($this->uri);
	include("app/nouns/page/".(($id)?"update":"create").".php");
} else if ($total == 0) { ?>
	<h2>page list</h2>
	<p>Nothing to display</p>
	<a class="button" href="<?php echo uri("page/create"); ?>">new</a>
<?php } else {
	$page = next($this->uri);
	empty_nan($page, 0);
	$list = $pages->get("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25")->GetRows();
	$shown = $pages->recordCount;
	?>
	<h2>Pages</h2>
	<?php if ($total > 25) { ?>
	<ul class="pages">
		<?php if ($page > 0) { ?>
		<li class="back"><a class="button" href="<?php echo uri("page/").($page-1); ?>">Back</a></li>
		<?php } for($i=0;$i<ceil($total/25);$i++) { ?>
		<li><a class="button<?php if($page == $i) { ?> active<?php } ?>" href="<?php echo uri("page/").$i; ?>"><?php echo $i+1; ?></a></li>
		<?php } if($page < ceil($total/25)-1) { ?>
		<li class="next"><a class="button" href="<?php echo uri("page/").($page+1); ?>">Next</a></li>
		<?php } ?>
		</ul>
	<?php } ?>
	<a class="button" href="<?php echo uri("page/create"); ?>">new</a>
	<table class="clear" id="page_list">
	<tr>
	<th>name</th><th>title</th><th>modified</th><th>order</th><th>options</th>
	</tr>
	<?php foreach($list as $entry) { ?>
		<tr id ="page_<?php echo $entry['id']; ?>">
			<td><?php echo $entry['name']; ?></td>	
			<td><a href="<?php echo uri($entry['name']); ?>"><?php echo $entry['title']; ?></a></td>
			<td><?php echo $entry['modified']; ?></td>
			<td><?php echo $entry['sort_order']; ?></td>
			<td>
				<form class="left" id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
					<input id="action[page]" name="action[page]" type="hidden" value="delete"/>
					<input type="hidden" name="page[id]" value="<?php echo $entry['id']; ?>"/>
					<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="delete"/>
				</form>
				<a class="button" href="<?php echo uri("page/update/$entry[id]"); ?>">edit</a>
			</td>
		</tr>
	<?php } ?>
	</table>
<?php } ?>
