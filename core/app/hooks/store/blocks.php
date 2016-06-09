<?php
namespace Starbug\Core;
class hook_store_blocks extends QueryHook {
	public function __construct(DatabaseInterface $db, InputFilterInterface $filter) {
		$this->db = $db;
		$this->filter = $filter;
	}
	function validate($query, $key, $value, $column, $argument) {
		$query->exclude($key);
		if ($query->mode == "insert") {
			$this->db->queue("blocks", array("type" => "text",  "region" => "content",  "position" => 1, "uris_id" => "", "content" => $this->filter->html($value['content-1'])));
		} else {
			$blocks = $this->db->query("blocks")->select("blocks.*")->condition($query->model."_id", $query->fields["id"])->all();
			foreach ($blocks as $block) {
				$key = $block['region'].'-'.$block['position'];
				if (isset($value[$key])) $this->db->queue("blocks", array("id" => $block['id'], "content" => $this->filter->html($value[$key])));
			}
		}
		return $value;
	}
	function before_delete($query, $column, $argument) {
		$id = $query->getId();
		$this->db->query("blocks")->condition($query->model."_id", $id)->delete();
	}
}
?>
