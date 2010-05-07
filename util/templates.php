<?php
$sb->import("util/form");
$sb->provide("util/templates");
function tag($tag, $self=false) {
	if (is_array($tag)) $name = array_shift($tag);
	else {
		$parts = explode("  ", $tag, 2);
		$name = $parts[0];
		if (count($parts) > 1) $tag = starr::star($parts[1]);
	}
	$echo = $tag['echo']; unset($tag['echo']);
	$content = $tag['content']; unset($tag['content']); $str = "";
	foreach($tag as $key => $value) $str .= " $key=\"$value\"";
	$return = ($self) ? "<$name$str />" : "<$name$str>$content</$name>";
	if ('false' !== $echo) echo $return;
	return $return;
}
function table_headers($arg) {
	$args = func_get_args();
	$th = "";
	foreach(array("thead", "tfoot") as $t) {
		$th .= "<$t><tr>";
		foreach($args as $arg) {
			$arg = starr::star("th  echo:false  content:".$arg);
			efault($arg['class'], str_replace(" ", "-", strtolower($arg['content']))."-col");
			$th .= tag($arg);
		}
		$th .= "</tr></$t>";
	}
	return $th;
}
function form($arg) {
	$args = func_get_args();
	$init = array_shift($args);
	$form = new form($init);
	$data = $form->open();
	foreach($args as $field) {
		$parts = explode("  ", $field, 2);
		$name = $parts[0];
		$data .= $form->$name($parts[1]);
	}
	$data .= "</form>";
	return $data;
}
?>
