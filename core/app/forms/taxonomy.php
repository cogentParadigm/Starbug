<?php
	$_POST['terms']['term'] = "Uncategorized";
	open_form("model:terms  action:create  uri:".uri("admin/taxonomies"));
	text("taxonomy");
	text("term");
	$cancel_url = uri("admin/taxonomies");
?>
<div class="btn-group"><button class="left positive btn btn-success" type="submit">Save</button><button class="negative cancel btn btn-danger"<?php if (!empty($cancel_url)) { ?> onclick="window.location='<?php echo $cancel_url; ?>';return false;"<?php } ?>>Cancel</button></div>
<?php close_form(); ?>
