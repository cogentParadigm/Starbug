<?php
namespace Starbug\Core;
class hook_macro_path extends MacroHook {
	function replace($macro, $name, $token, $data) {
		if ($name == "token") {
			$entity = query("entities")->condition("name", $data['uris']['type'])->one();
			$pattern = (empty($entity['url_pattern'])) ? "[uris:path]" : $entity['url_pattern'];
			return $macro->replace($pattern, $data);
		}
		return $token;
	}
}
?>
