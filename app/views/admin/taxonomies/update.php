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
		if (!empty($_GET['term']) && is_numeric($_GET['term'])) {
			assign("id", $_GET['term']);
			render("update");
		} else render("create");
	?>
</div>
<div style="width:500px;" class="left">
	<h1>Terms in '<?= $label; ?>'</h1>
	<div class="success" id="order_success" style="display:none">Term order updated successfully</div>
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
							dojo.style('order_success', 'display', 'block');
							setTimeout(function(){dojo.fadeOut({node:'order_success'}).play()}, 2000);
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

