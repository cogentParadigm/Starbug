<h1>New Taxonomy</h1>	
<?php
	$_POST['terms']['term'] = "Uncategorized";
	open_form("model:terms  action:create  uri:".uri("admin/taxonomies"));
	text("taxonomy");
	hidden("term");
	$cancel_url = uri("admin/taxonomies");
?>
<div class="btn-group"><button class="left positive btn" type="submit">Save</button><button class="negative cancel btn"<?php if (!empty($cancel_url)) { ?> onclick="window.location='<?= $cancel_url; ?>';return false;"<?php } ?>>Cancel</button></div>
<?php close_form(); ?>
