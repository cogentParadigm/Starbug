<div class="panel panel-default">
	<div class="panel-heading"><strong>Settings</strong></div>
	<div class="panel-body">
		<div class="col-md-3">
			<?php
				assign("attributes", array("id" => "settings-menu", "class" => "nav-tabs nav-stacked", "data-spy" => "affix", "data-dojo-props" => "offset:90", "data-dojo-type" => "bootstrap/Affix", "style" => "top:10px"));
				assign("menu", "");
				assign("taxonomy", "settings_category");
				render("menu");
			?>
		</div>
		<div class="col-md-9">
			<?php
				render_display("form", "settings", "form", array("action" => "update", "cancel_url" => "admin"));
				//render_form("settings", array(..));
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	require(["dojo/query", "put-selector/put"], function(query, put) {
		query('#settings-menu > li > a').forEach(function(node) {
			put(node, '[href="<?php echo uri("admin/settings#"); ?>'+node.innerText+'"]');
			put(node, 'i.fa.fa-chevron-right[style="float:right;font-size:1.6em;line-height:1.4em"]');
		});
	});
</script>
