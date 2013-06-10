<?php efault($type, "Page"); if (success("uris", "update")) { ?>
<div class="success"><?php echo $type; ?> updated successfully</div>
<?php } ?>
<script type="text/javascript">
var old_link = '<?php if ($action == "update") echo $_POST['uris']['path']; ?>';
var old_title = '<?php if ($action == "update") echo $_POST['uris']['title']; ?>';
function edit_eip_text(evt) {
	evt.preventDefault();
	var editable = evt.target;
	if (editable.firstChild != null && editable.firstChild.nodeType == 3) {
		if (editable.firstChild.textContent == "Cancel") {
				editable.parentNode.innerHTML = dojo.byId("path").value = old_link;
		} else if (editable.firstChild.textContent == "Save") {
				editable.parentNode.innerHTML = old_link = dojo.byId("path").value = editable.parentNode.firstChild.value;
		} else editable.innerHTML = '<input type="text" class="text" value="'+editable.innerHTML+'" name="'+editable.parentNode.id+'" /><a href="" class="save_editable">Save</a><a href="" class="cancel_editable">Cancel</a>';
	}
}
function name_saved(args) {
	args.args.node.innerHTML = old_link = dojo.byId("path").value = args.args.data.uris[0].path;
}
var update_link = true;
function title_onchange(evt) {
	var title = evt.target;
	console.log(title);
	var permalink = dojo.query("#permalink .editable")[0];
	console.log(permalink);
	var namebox = dojo.byId("path");
	if ((old_title.replace(/ /g, '-').replace("'", '').replace(".", '').toLowerCase()) == old_link) permalink.innerHTML = namebox.value = old_link = title.value.replace(/ /g, '-').replace("'", '').replace(".", '').toLowerCase();
	old_title = title.value;
}
function editable_onchange(evt) {
	var editable = evt.target.parentNode;
	var textbox = dojo.byId('path');
	textbox.value = editable.innerHTML;
}
<?php if ($action == "update") { ?>
function apply_tags() {
	sb.xhr('<?php echo uri("api/uris.terms via uris_tags.json?select=DISTINCT term,slug&where=uris.id=".$_POST['uris']['id'], "u"); ?>', {
		content: {
			'action[uris]': 'apply_tags',
			'uris[id]': '<?php echo $_POST['uris']['id']; ?>',
			'tags': dojo.byId('tagbox').value
		},
		method: 'post',
		handleAs: 'json',
		action: display_tags,
		node: dojo.byId('applied_tags')
	});
}
function remove_tag(tag) {
	sb.xhr('<?php echo uri("api/uris.terms via uris_tags.json?select=DISTINCT term,slug&where=uris.id=".$_POST['uris']['id'], "u"); ?>', {
		content: {
			'action[uris]': 'remove_tag',
			'uris[id]': '<?php echo $_POST['uris']['id']; ?>',
			'tag': tag
		},
		method: 'post',
		handleAs: 'json',
		action: display_tags,
		node: dojo.byId('applied_tags')
	});
}
function display_tags(data, args) {
	console.log(arguments);
	var list = "";
	for(var i=0;i<data.length;i++) {
		console.log(data[i]);
		var item = data[i];
		list += '<li><a href="javascript:remove_tag(\''+item.term+'\');">x</a> '+item.term+'</li>\n';
	}
	args.node.innerHTML = list;
}
<?php } ?>
require(['dojo/query', 'dojo/domReady!'], function($) {
	var editable = $(".editable");
	editable.on("click", edit_eip_text);
	editable.on('change', editable_onchange);
	$("#title").on('change', title_onchange);
});
</script>
<?php
	js("dijit/form/Textarea");
	$groups = config("groups");
	$collectives = array_merge(array("everybody" => 0), $groups);
	$parents = query("uris", "where:prefix='app/views/' && type='Page'");
	$kids = array(array());
	foreach($parents as $u) $kids[$u['parent']][] = $u;
	function parent_options($u, $k, $l=0) {
		$arr = array();
		$key = $u['path'];
		for($i=0;$i<$l;$i++) $key = "-".$key;
		$arr[$key] = $u['id'];
		if (!empty($k[$u['id']])) foreach ($k[$u['id']] as $kid) $arr = array_merge_recursive($arr, parent_options($kid, $k, $l+1));
		return $arr;
	}
	$parent_ops = array(" -- " => 0);
	foreach($kids[0] as $child) $parent_ops = array_merge_recursive($parent_ops, parent_options($child, $kids));
	
	$templates = array("default" => $type);
	if (logged_in("root")) $templates["View"] = "View";
	foreach (array("core/app/layouts/", "app/themes/".settings("theme")."/layouts/", "app/layouts") as $dir) {
		if (file_exists($dir) && false !== ($handle = opendir($dir))) {
			while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
			closedir($handle);
		}
	}
	
	efault($_POST['uris']['type'], $type);
	efault($_POST['uris']['status'], 4);
	efault($_POST['uris']['prefix'], "app/views/");
	efault($_POST['uris']['collective'], "0");
	$base_uri = ($type == "Post") ? uri("blog/", "f") : uri("", "f");
?>

<? open_form("model:uris  action:$action  url:$submit_to", "class:pages_form"); ?>
	<input type="hidden" name="type" value="<?php echo $type; ?>"/>
	<div class="field">
		<? text("title  class:round title"); ?>
		<label id="link-label">Permalink:</label>
		<span id="permalink" class="link-span"><?= $base_uri; ?><span class="editable"><?= (($_POST['uris']['path']) ? $_POST['uris']['path'] : ".." ); ?></span></span>
		<? if (errors('uris[path]')) echo "<span class=\"clear error\">".reset(errors('uris[path]'))."</span><br />"; ?>
		<div class="round infield">
			<?php
				$status_list = config("statuses");
				unset($status_list['deleted']);
				unset($status_list['pending']);
				unset($collectives['root']);
				select("type  label:Template", $templates);
				select("status", $status_list);
				select("collective  label:Access", $collectives);
				select("parent", $parent_ops);
				multiple_category_select("categories");
				button(ucwords($action), "class:big round left button");
			?>
				<br class="clear"/><br/><br/>
				<label>Tags</label>
				<input type="text" id="tagbox" class="text left" style="width:65%" /><a class="round right button" href="javascript:apply_tags();">apply</a>
				<ul id="applied_tags">
					<?php foreach(query("uris_tags,terms", "select:DISTINCT term, slug  where:!(terms.status & 1) and uris_tags.uris_id='".$_POST['uris']['id']."'") as $tag) { ?>
						<li><a href="javascript:remove_tag('<?php echo $tag['term']; ?>');">x</a> <?php echo $tag['term']; ?></li>
					<?php } ?>
				</ul>
		</div>
		<div class="left">
			<?php text("path  nolabel:true  style:width:630px;display:none"); ?>
		</div>
	</div>
	<?php
	if ($action == "update") {
		$count = query("blocks", "select:COUNT(*) as count  where:uris_id=? && !(status & 1)  limit:1", array($_POST['uris']['id']));
		if ($count['count'] == 0) store("blocks", "uris_id:".$_POST['uris']['id']."  type:text  region:content  position:1");
		$containers = query("blocks", "where:uris_id=? && !(status & 1)  orderby:position ASC", array($_POST['uris']['id']));
		$l = new form("model:new-block");
		$r = new form("model:remove-block");
		foreach($containers as $container) { ?>
			<fieldset style="clear:left;width:82%">
				<legend><?php echo ucwords(str_replace("_", " ", $container['region'])); ?></legend>
				<?php
					assign("id", $_POST['uris']['id']);
					assign("region", $container['region']);
					assign("position", $container['position']);
					render("form/block/$container[type]");
				?>
			</fieldset>
		<?php } ?>
	<?php } ?>
	<fieldset style="margin-top:20px;width:82%">
		<legend>SEO</legend>
		<br/>
		<?php textarea("description  label:Meta Description  class:plain  style:width:100%  data-dojo-type:dijit.form.Textarea"); ?>
		<br/>
		<?php textarea("meta_keywords  label:Meta Keywords  class:plain  style:width:100%  data-dojo-type:dijit.form.Textarea"); ?>
		<br/>
		<?php textarea("meta  label:Custom Meta Tags  class:plain  style:width:100%  data-dojo-type:dijit.form.Textarea"); ?>
		<br/>
		<?php text("canonical  label:Canonical URL  style:width:100%"); ?>
		<br/>
		<?php text("breadcrumb  label:Breadcrumbs Title  style:width:100%"); ?>
	</fieldset>
<? close_form(); ?>
