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
						unset($_POST['menus']['uris_id']);
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
		<ol id="menu_items" class="menu_items" data-dojo-type="dojo.dnd.Source" data-dojo-props="withHandles: true" jsId="menulist">
		<?php foreach ($links as $link) { ?>
			<li class="dojoDndItem" id="menu_item_<?php echo $link['id']; ?>"><a href="#" class="dojoDndHandle"></a><a href="<?php echo uri($link['path']); ?>"><?php echo $link['title']; ?></a></li>
		<?php } ?>
		</ol>
	<?php } ?>
</div>
<script type="text/javascript">
	function changeOrder(source, nodes, copy) {
		var menus_id = '<?php echo $id; ?>';
		var uris_menus_id = dojo.attr(nodes[0], 'id').substr(10);
		var new_position = dojo.indexOf(dojo.query('li',nodes[0].parentNode),nodes[0]);
		dojo.xhrPost({
			url: WEBSITE_URL+'api/uris_menus.json',
			content: {'action[uris_menus]':'create', 'uris_menus[id]':uris_menus_id, 'uris_menus[menus_id]':menus_id, 'uris_menus[position]':new_position}
		});
	}
	require(['dojo', 'dojo/on', 'dojo/domReady!'], function(dojo, on) {
		dojo.parser.parse();
		on(menulist, 'Drop', changeOrder);
	});
</script>
