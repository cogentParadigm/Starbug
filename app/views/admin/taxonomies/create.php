<h1>New Taxonomy</h1>	
<?php
	open_form("model:terms  action:create  uri:admin/taxonomies");
	text("taxonomy");
	text("term  label:Initial Term");
	$cancel_url = uri("admin/taxonomies");
?>
<div class="field"><button class="left positive" type="submit">Save</button><button class="negative cancel button"<?php if (!empty($cancel_url)) { ?> onclick="window.location='<?= $cancel_url; ?>'"<?php } ?>>Cancel</button></div>
<?php close_form(); ?>
