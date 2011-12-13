<?php js("dojo/dnd/Source"); ?>
<?php if (success("menus", "create")) { ?>
	<div class="success">Menu <?= (empty($_POST['menus']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
<div class="left" style="margin-right:50px">
	<?php
		$id = end($request->uri);
		assign("model", "menus");
		assign("id", $id);
		assign("uri", "admin/menus/update/$id");
		render("update");
	?>
	<div class="uris_list">
		<ul>
			<?php foreach (query("uris") as $uri) { ?>
			<li>
				<div class="right">
					<?php
						open_form("model:menus  action:add_uri");
						hidden("uris_id  default:$uri[id]");
						button("add", "class:link  style:margin:0");
						close_form();
					?>
				</div>
				<?php echo $uri['title']; ?>
				<br class="clear"/>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
<div style="width:500px;" class="left">
	<h1>Links</h1>
	<?php $links = query("uris_menus,uris", "select:uris_menus.*,uris.title,uris.path  where:menus_id=?  orderby:position ASC", array($id)); ?>
	<?php if (empty($links)) { ?>
		<p>There are no links in this menu yet.</p>
	<?php } else { ?>
		<ul class="menu_items" data-dojo-type="dojo.dnd.Source">
		<?php foreach ($links as $link) { ?>
			<li class="dojoDndItem"><a href="<?php echo uri($link['path']); ?>"><?php echo $link['title']; ?></a></li>
		<?php } ?>
		</ul>
	<?php } ?>
</div>
