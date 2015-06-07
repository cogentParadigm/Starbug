<?php
class hook_store_terms extends QueryHook {
	protected $taxonomy;
	function __construct(TaxonomyInterface $taxonomy) {
		$this->taxonomy = $taxonomy;
	}
	function after_store(&$query, $key, $value, $column, $argument) {
		$name = $query->model;
		$id = $query->getId();
		$category_column_info = sb($name)->hooks[$column];
		if (empty($category_column_info['taxonomy'])) $category_column_info['taxonomy'] = $name."_".$column;
		$tags = empty($category_column_info['table']) ? $name."_".$column : $category_column_info['table'];
		$mentioned_tags = array();
		$remove_unmentioned_tags = false;
		if (!is_array($value)) $value = explode(",", preg_replace("/[,\s]+/", ",", $value));
		foreach ($value as $tag) {
			if ($tag === "-~") {
				//remove all tags not mentioned
				$remove_unmentioned_tags = true;
			} else if (0 === strpos($tag, "-")) {
				//remove tag
				$this->taxonomy->untag($name, $id, $column, substr($tag, 1));
				$mentioned_tags[] = substr($tag, 1);
			} else {
				//add tag
				//echo "tag('".$name."', ".$id.", '".$column."', '".$tag."')";
				$this->taxonomy->tag($name, $id, $column, $tag);
				$mentioned_tags[] = $tag;
			}
		}
		if ($remove_unmentioned_tags) {
			$query = query($tags)->condition($name."_id", $id);
			if (!empty($mentioned_tags)) {
				$query->condition($column."_id", $mentioned_tags, "!=")
					->condition($column."_id.slug", $mentioned_tags, "!=")
					->condition($column."_id.term", $mentioned_tags, "!=");
			}
			$query->delete();
		}
	}
}
?>
