<script type="text/javascript">
function edit_eip_text(id, input_name) {
	var editable = dojo.query('#'+id+' .editable')[0];
	var field = dojo.create("input");
	field.type = 'text';
	field.class = 'text';
	field.value = editable.innerHTML;
	field.name = input_name;
	dojo.place(field, editable, "after");
	dojo.destroy(editable);
}
</script>
<?php
$templates = array(); $layouts = array(); $leafs = array();
if (false !== ($handle = opendir("app/nouns/templates/"))) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
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
		<?php sb::load("core/jsforms"); ?>
<?php
	$sb->import("util/form");
	$title_errors = array("title" => "Please enter a Title");
	$name_errors = array("name" => "Please enter a Name", "nameExists" => "That Name already exists");
	$layout_errors = array("layout" => "Please enter a Layout");
	$fields = array(
			"topfield" => array("type" => "field",
					"title" => array("type" => "text", "length" => "128", "errors" => $title_errors),
					"name" => array("type" => "custom", "content" => "<label id=\"link-label\">Permalink:</label><span id=\"name\" class=\"link-span\">".uri("")."<span class=\"editable\">".(($_POST['pages']['name']) ? $_POST['pages']['name'] : "" )."</span></span>"),
					//"name" => array("type" => "eip_text", "length" => "64", "unique" => "true", "label" => "Permalink:", "errors" => $name_errors),
					"infield" => array("type" => "field", "class" => "infield",
							"template" => array("type" => "select", "options" => $templates),
							"layout" => array("type" => "select", "options" => $layouts, "errors" => $layout_errors),
							"Save" => array("type" => "submit", "class" => "big left button")
					)
			)
	);
	echo form::render("pages", "post", $action, $submit_to, $fields);
?>
