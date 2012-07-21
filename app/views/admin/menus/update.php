<?php js("dojo/dnd/Source"); ?>
<?php if (success("menus", "create")) { ?>
	<div class="success">Menu <?= (empty($_POST['menus']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
<div class="left" style="margin-right:50px">
	<?php
		render("update");
	?>
	<br class="clear"/>
	<h1>Add Items</h1>
	<?php
	$links = query("uris_menus,uris", "select:uris_menus.*,uris.title,uris.path  where:menus_id=?  orderby:position ASC", array($id));
	$parents = array();
	$kids = array();
	foreach ($links as $link) {
		if ($link['parent'] == 0) $parents[] = $link;
		else {
			efault($kids[$link['parent']], array());
			$kids[$link['parent']][] = $link;
		}
	}
	function get_option_list($top, $all, $prefix="", $options=array()) {
		foreach ($top as $t) {
			$options[$prefix.$t['title']] = $t['id'];
			if (!empty($all[$t['id']])) $options = array_merge($options, get_option_list($all[$t['id']], $all, $prefix."-", $options));
		}
		return $options;
	}
	$option_list = get_option_list($parents, $kids);
	?>
	<label>Parent</label>
	<select id="parent_select" name="parent_select" style="width:300px">
			<option value="0"></option>
		<?php foreach ($option_list as $c => $v) { ?>
			<option value="<?= $v; ?>"><?= $c; ?></option>
		<?php } ?>
	</select>
	<div class="uris_list">
		<ul>
			<?php foreach (query("uris", "where:prefix='app/views/' && type='Page'") as $uri) { ?>
			<li>
				<div class="right">
					<?php
						unset($_POST['menus']['uris_id']);
						open_form("model:menus  action:add_uri");
						hidden("uris_id  id:uris_id_$uri[id]  default:$uri[id]");
						hidden("parent  class:parent_id  id:parent_$uri[id]  defalut:0");
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
	<?php $links = query("uris_menus,uris", "select:uris_menus.*,uris.title,uris.path  where:menus_id=? && uris_menus.parent=0  orderby:position ASC", array($id)); ?>
	<?php if (empty($links)) { ?>
		<p>There are no links in this menu yet.</p>
	<?php } else {
		assign("links", $links);
		render("sortable-menu");
	} ?>
</div>
<script type="text/javascript">
	require(['dojo/query', 'dojo/_base/connect', 'dojo/domReady!'], function($, connect) {
		$('#parent_select').on('change', function() {
			$('.parent_id').attr('value', dojo.attr('parent_select', 'value'));
		});
		connect.subscribe("/dnd/drop", function(source, nodes, copy) {
			var menus_id = '<?php echo $_POST['menus']['id']; ?>';
			var uris_menus_id = dojo.attr(nodes[0], 'data-menu-id');
			var new_position = dojo.indexOf(dojo.query('li',nodes[0].parentNode),nodes[0]);
			dojo.xhrPost({
				url: WEBSITE_URL+'api/uris_menus.json',
				content: {'action[uris_menus]':'create', 'uris_menus[id]':uris_menus_id, 'uris_menus[menus_id]':menus_id, 'uris_menus[position]':new_position}
			});			
		});
	});
</script>
