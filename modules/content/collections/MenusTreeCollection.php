<?php
namespace Starbug\Content;
use Starbug\Core\MenusTreeCollection as ParentCollection;
class MenusTreeCollection extends ParentCollection {
	public function build($query, &$ops) {
		$query->select("menus.pages_id.title");
		return parent::build($query, $ops);
	}
	public function filterRows($rows) {
		foreach ($rows as $idx => $item) {
			if (empty($item['content']) && !empty($item['title'])) $rows[$idx]['content'] = $item['title'];
		}
		return parent::filterRows($rows);
	}
}
