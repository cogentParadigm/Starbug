<script type="text/javascript">
function edit_eip_text(args) {
	var editable = args.args.evt.target;
	if ((editable.firstChild != null) && (editable.firstChild.textContent == "Cancel")) editable.parentNode.innerHTML = '<?php echo $_POST['pages']['name']; ?>';
	else if ((editable.firstChild != null) && (editable.firstChild.textContent == "Save")) sb.xhr({ args : { url : '<?php echo uri("api/pages/get.json"); ?>', content: { 'action[pages]' : "change_name", "new_name" : editable.parentNode.firstChild.value, "old_name" : "<?php echo $_POST['pages']['name']; ?>" }, method: 'post', action: name_saved, handleAs: 'json', node: args.args.node} });
	else editable.innerHTML = '<input type="text" class="text" value="'+editable.innerHTML+'" name="'+editable.parentNode.id+'" /><a href="" class="save_editable">Save</a><a href="" class="cancel_editable">Cancel</a>';
}
function name_saved(args) {
	args.args.node.innerHTML = args.args.data.pages[0].name;
}
</script>
<?php
$sb->import("util/dojo");
global $dojo;
$dojo->attach(".editable", "edit_eip_text", "node:evt.target.parentNode");

$templates = array(); $layouts = array(); $leafs = array(); $leaf_types = array();
if (false !== ($handle = opendir("app/nouns/templates/"))) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
	closedir($handle);
}
if (false !== ($handle = opendir("app/nouns/leafs/"))) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $leaf_types[$file] = $file;
	closedir($handle);
}
if (false !== ($handle = opendir("app/nouns/layouts/"))) {
	while (false !== ($file = readdir($handle))) {
		if ((strpos($file, ".") !== 0)) {
			$name = substr($file, 0, strpos($file, "."));
			$layouts[$name] = $name;
			$layout = file_get_contents("app/nouns/layouts/".$file);
			$start = strpos($layout, "* containers:")+13;
			$end = strpos($layout, "\n", $start);
			$leafs[$name] = explode(",", trim(substr($layout, $start, $end-$start)));
		}
	}
	closedir($handle);
}
?>
		<?php sb::load("core/app/plugins/jsforms"); ?>
<?php
	$sb->import("util/form");
	$title_errors = array("title" => "Please enter a Title");
	$name_errors = array("name" => "Please enter a Name", "nameExists" => "That Name already exists");
	$layout_errors = array("layout" => "Please enter a Layout");
	$fields = array("div	class:field	fields:$0");
	$topfield = array(
		"title" => "text	length:128	errors:$1",
		"label	id:link-label	content:Permalink:",
		"span	id:permalink	class:link-span	content:".uri("")."<span class=\"editable\">".(($_POST['pages']['name']) ? $_POST['pages']['name'] : "" )."</span>",
		"div	class:infield	fields:$2"
	);
	$infield = array(
		"template" => "select	options:$3",
		"layout" => "select	options:$4	errors:$5",
		"status" => "select	options:$6",
		"Save" => "submit	class:big left button"
	);
	$extras = array($topfield, $title_errors, $infield, $templates, $layouts, $layout_errors, $this->statuses, $leaf_types);
	efault($_POST['pages']['layout'], "2-col-right");
	$atvar = 8;
	foreach($leafs[$_POST['pages']['layout']] as $container) {
		$fields[$container] = "fieldset	legend:$container	fields:$".$atvar;
		$fieldset = $sb->get("pages")->fields($container, $_POST['pages']['name']);
		$fieldset[$container."-new-leaf"] = "select	options:$7	nolabel:true	class:left";
		$fieldset[] = "a	class:inline_button	content:Add Leaf to $container";
		$extras[] = $fieldset;
		$atvar++;
	}
	echo str_replace("%tab%", "\t", form::build("pages", "action:$action", $fields, $extras));
?>
