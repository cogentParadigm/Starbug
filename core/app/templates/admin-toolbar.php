<?php
$options = schema($model);
$new_label = "New $options[singular_label] <b class=\"icon-plus\"></b>";
$new_attrs = "class:btn";
?>
<div class="btn-toolbar">
		<?php
			render_form(array($model."/search", "search"));
			link_to("Export CSV", "", array("href" => "javascript:window.location.href = ".$model."_grid.store.last_query.replace('json', 'csv');", "class" => "btn"));
			if ($dialog) link_to($new_label, "", $new_attrs."  href:javascript:(function(){".$model."_form.show();return false;})()");
			else link_to($new_label, $request->path."/create", $new_attrs);
		?>
</div>
