<?php if (success("uris", "update")) { ?>
<div class="success">Page updated successfully</div>
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
	if ((old_title.replace(/ /g, '-').replace("'", '').toLowerCase()) == old_link) permalink.innerHTML = namebox.value = old_link = title.value.replace(/ /g, '-').replace("'", '').toLowerCase();
	old_title = title.value;
}
function editable_onchange(evt) {
	var editable = evt.target.parentNode;
	var textbox = dojo.byId('path');
	textbox.value = editable.innerHTML;
}
<?php if ($action == "update") { ?>
function apply_tags() {
	sb.xhr({
		url: '<?php echo uri("api/uris.tags.json"); ?>',
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
	sb.xhr({
		url: '<?php echo uri("api/uris.tags.json"); ?>',
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
function display_tags(args) {
	console.log(arguments);
	var list = "";
	for(var i=0;i<args.args.data.uris.length;i++) {
		console.log(args.args.data.uris[i]);
		var item = args.args.data.uris[i];
		list += '<li><a href="javascript:remove_tag(\''+item.tag+'\');">x</a> '+item.tag+'</li>\n';
	}
	args.args.node.innerHTML = list;
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
	$collectives = array_merge(array("everybody" => 0), $request->groups);
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

	$templates = array("Page" => "Page"); $containers = array("content");
	if (logged_in("root")) $templates["View"] = "View";
	if (false !== ($handle = opendir("app/templates/"))) {
		//while (false !== ($file = readdir($handle))) if (((strpos($file, ".") !== 0)) && ($file != "options")) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
		closedir($handle);
	}

	efault($_POST['uris']['type'], "Page");
	efault($_POST['uris']['status'], 4);
	efault($_POST['uris']['prefix'], "app/views/");
	efault($_POST['uris']['collective'], "0");
?>

<? open_form("model:uris  action:$action  url:$submit_to", "class:pages_form"); ?>
	<div class="field">
		<? text("title  class:round title"); ?>
		<label id="link-label">Permalink:</label>
		<span id="permalink" class="link-span"><?= uri("", "f"); ?><span class="editable"><?= (($_POST['uris']['path']) ? $_POST['uris']['path'] : ".." ); ?></span></span>
		<? if (errors('uris[path]')) echo "<span class=\"clear error\">".reset(errors('uris[path]'))."</span><br />"; ?>
		<?php if ($action == "update") {/* ?>
			<div class="round infield" style="float:left;clear:left;width:250px">
				<h3>Tags</h3>
				<input type="text" id="tagbox" class="text left" style="width:195px" /><a class="round right button" href="javascript:apply_tags();">apply</a>
				<ul id="applied_tags">
					<?php foreach(query("uris,tags", "select:DISTINCT tag, raw_tag  where:uris.id='".$_POST['uris']['id']."'") as $tag) { ?>
						<li><a href="javascript:remove_tag('<?php echo $tag['tag']; ?>');">x</a> <?php echo $tag['tag']; ?></li>
					<?php } ?>
				</ul>
			</div>
		<?php */} ?>
		<div class="round infield">
			<?php
				$status_list = $request->statuses;
				unset($status_list['deleted']);
				unset($status_list['pending']);
				unset($collectives['root']);
				select("type", $templates);
				select("status", $status_list);
				select("collective  label:Access", $collectives);
				select("parent", $parent_ops);
				button(ucwords($action), "class:big round left button");
			?>
		</div>
		<div class="round infield" style="clear:right">
			<?php category_select("category  writable:true"); ?>
		</div>
		<div class="left">
			<?php text("path  nolabel:true  style:width:630px;display:none"); ?>
		</div>
	</div>
	<?php
	if (($action == "update") && (!empty($containers))) {
		$l = new form("model:new-block");
		$r = new form("model:remove-block");
		foreach($containers as $container) { ?>
			<fieldset style="clear:left;width:82%">
				<legend><?php echo $container; ?></legend>
				<?php
					assign("id", $_POST['uris']['id']);
					assign("region", $container);
					assign("position", "1");
					render("form/block/text");
				?>
			</fieldset>
		<?php } ?>
	<?php } ?>
<? close_form(); ?>
<?php $sb->import("util/tinymce"); ?>
