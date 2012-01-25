<h1>New Taxonomy</h1>	
<?php
	$_POST['terms']['term'] = "Uncategorized";
	open_form("model:terms  action:create  uri:".uri("admin/taxonomies"));
	text("taxonomy");
	hidden("term");
	$cancel_url = uri("admin/taxonomies");
?>
<div class="field"><button class="left positive" type="submit">Save</button><button class="negative cancel button"<?php if (!empty($cancel_url)) { ?> onclick="window.location='<?= $cancel_url; ?>'"<?php } ?>>Cancel</button></div>
<?php close_form(); ?>
