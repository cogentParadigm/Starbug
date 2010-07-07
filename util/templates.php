<?php
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
?>
