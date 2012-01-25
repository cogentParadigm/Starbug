<?php
	$taxonomy = $_POST['terms']['taxonomy'] = end($request->uri);
	$label = ucwords(str_replace("_", " ", $taxonomy));
?>
<?php if (success("menus", "create")) { ?>
	<div class="success">Taxonomy <?= (empty($_POST['menus']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
<div class="left" style="margin-right:50px">
	<?php
		if (!errors("terms")) {
			unset($_POST['terms']);
			$_POST['terms'] = array('taxonomy' => $taxonomy);
		}
		assign("model", "terms");
		assign("uri", "admin/taxonomies/update/$taxonomy");
		assign("parent_options", "taxonomy:$taxonomy  optional:");
		render("create");
	?>
</div>
<div style="width:500px;" class="left">
	<h1>Terms in '<?= $label; ?>'</h1>
	<?php $terms = query("terms", "where:taxonomy=? && parent=0  orderby:position ASC", array($taxonomy)); ?>
	<?php if (empty($terms)) { ?>
		<p>There are no terms in this taxonomy yet.</p>
	<?php } else {
		assign("terms", $terms);
		render("terms-list");
		echo '<button>Save</button>';
	} ?>
</div>
<script type="text/javascript">
	require(['dojo/query', 'dojo/domReady!'], function($) {
		function update_order(evt) {
			var taxonomy = '<?php echo $taxonomy; ?>';
			var terms_id = dojo.attr(evt.currentTarget, 'name');
			var terms_parent = dojo.attr(evt.currentTarget, 'data-term-parent');
			var new_position = dojo.attr(evt.currentTarget, 'value');
			dojo.xhrPost({
				url: WEBSITE_URL+'api/terms.json',
				content: {'action[terms]':'create', 'terms[id]':terms_id, 'terms[taxonomy]':taxonomy, 'terms[parent]':terms_parent, 'terms[position]':new_position},
				load: function() {
					dojo.xhrGet({
						url: WEBSITE_URL+'admin/taxonomies/update/<?php echo $taxonomy; ?>.xhr',
						load: function(data) {
							$("#content .box .inside").attr('innerHTML', data);
							registerEvents();
						}
					});
				}
			});			
		}
		function registerEvents() {
			$('.menu-position-field').on('change', update_order);
		}
		registerEvents();
	});
</script>

