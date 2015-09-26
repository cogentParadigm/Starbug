<?php
namespace Starbug\Core;
class hook_macro_path extends MacroHook {
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function replace($macro, $name, $token, $data) {
		if ($name == "token") {
			$entity = $this->db->query("entities")->condition("name", $data['uris']['type'])->one();
			$pattern = (empty($entity['url_pattern'])) ? "[uris:path]" : $entity['url_pattern'];
			return $macro->replace($pattern, $data);
		}
		return $token;
	}
}
?>
