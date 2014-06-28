<?php efault($type, "Page"); if (success("uris", "update")) { ?>
<div class="alert alert-success"><?php echo $type; ?> updated successfully</div>
<?php } ?>
<?php
	js("dijit/form/Textarea");
	$templates = array("default" => $type);
	if (logged_in("root")) $templates["View"] = "View";
	foreach (array("core/app/layouts/", "app/themes/".settings("theme")."/layouts/", "app/layouts") as $dir) {
		if (file_exists($dir) && false !== ($handle = opendir($dir))) {
			while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
			closedir($handle);
		}
	}
	
	efault($_POST['uris']['type'], $type);
	efault($_POST['uris']['prefix'], "app/views/");
	$base_uri = ($type == "Post") ? uri("blog/", "f") : uri("", "f");
?>

<? open_form("model:uris  action:$action  url:$submit_to", "class:pages_form"); ?>
	<input type="hidden" name="type" value="<?php echo $type; ?>"/>
	<div class="row">
		<div class="col-md-9">
			<? text("title  class:title"); ?>
			<?php
			if ($action == "update") {
				$count = query("blocks", "select:COUNT(*) as count  where:uris_id=? && !(status & 1)  limit:1", array($_POST['uris']['id']));
				if ($count['count'] == 0) store("blocks", "uris_id:".$_POST['uris']['id']."  type:text  region:content  position:1");
				$containers = query("blocks", "where:uris_id=? && !(status & 1)  orderby:position ASC", array($_POST['uris']['id']));
				$l = new form("model:new-block");
				$r = new form("model:remove-block");
				foreach($containers as $container) { ?>
					<label><?php echo ucwords(str_replace("_", " ", $container['region'])); ?></label>
					<?php
						assign("id", $_POST['uris']['id']);
						assign("region", $container['region']);
						assign("position", $container['position']);
						render("form/block/$container[type]");
					?>
				<?php } ?>
			<?php } else if ($action == "create") { ?>
					<label>Content</label>
					<?php
						assign("region", "content");
						assign("position", 1);
						render("form/block/text");
					?>
			<?php } ?>
			<?php
				file_select("images  size:0");
			?>
		</div>
		<div class="col-md-3">
			<?php
				select("type  label:Template", $templates);
				category_select("statuses  label:Status  taxonomy:statuses  default:pending");
				multiple_category_select("groups  taxonomy:groups");
				multiple_category_select("categories");
				tag_select("tags");
			?>
		</div>
	</div>
	<div class="row" style="margin-top:20px;margin-bottom:20px">
		<div class="col-sm-12">
			<div data-dojo-type="dijit/layout/TabContainer" data-dojo-props="doLayout:false, tabPosition:'left-h'" style="width: 100%; height: 100%">
				<div data-dojo-type="dijit/layout/ContentPane" title="URL path"<?php if (empty($_GET['tab'])) { ?> data-dojo-props="selected:true"<?php } ?> style="min-height:200px">
					<?php text("path  label:URL path  info:Leave empty to generate automatically"); ?>
				</div>
				<div data-dojo-type="dijit/layout/ContentPane" title="Meta tags"<?php if (empty($_GET['tab'])) { ?> data-dojo-props="selected:true"<?php } ?>>
					<?php textarea("description  label:Meta Description  class:plain  style:width:100%  data-dojo-type:dijit.form.Textarea"); ?>
					<?php textarea("meta_keywords  label:Meta Keywords  class:plain  style:width:100%  data-dojo-type:dijit.form.Textarea"); ?>
					<?php text("canonical  label:Canonical URL  style:width:100%"); ?>
					<?php textarea("meta  label:Custom Meta Tags  class:plain  style:width:100%  data-dojo-type:dijit.form.Textarea"); ?>
				</div>
				<div data-dojo-type="dijit/layout/ContentPane" title="Breadcrumbs"<?php if (empty($_GET['tab'])) { ?> data-dojo-props="selected:true"<?php } ?> style="min-height:200px">
					<?php text("breadcrumb  label:Breadcrumbs Title  style:width:100%"); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="btn-group">
				<?php button("Save", "class:btn-success"); ?>
				<button type="button" class="cancel btn btn-danger" onclick="window.location='<?= uri("admin/pages"); ?>'">Cancel</button>
			</div>
		</div>
	</div>	
<? close_form(); ?>
