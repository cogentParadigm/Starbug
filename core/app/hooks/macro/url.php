<?php
namespace Starbug\Core;
class hook_macro_url extends MacroHook {
	function replace($macro, $name, $token, $data) {
		return isset($data['url_flags']) ? uri($name, $data['url_flags']) : uri($name);
	}
}
?>
