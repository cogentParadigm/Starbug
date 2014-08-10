<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model base
 * @ingroup models
 */
class <?= ucwords($name); ?>Model extends Table {

	var $hooks = array(<? $count = 0; foreach ($fields as $column => $field) { if (!empty($field)) { $fcount = 0; if ($count > 0) echo ','; $count++; echo "\n"; ?>
		"<?= $column; ?>" => array(<? foreach ($field as $k => $v) { ?><? if ($fcount > 0) echo ", "; $fcount++ ?>"<?= $k; ?>" => "<?= $v; ?>"<? } ?>)<? } } echo "\n"; ?>
	);

	function init() {<? foreach ($fields as $column => $field) { foreach ($field as $k => $v) { if ($k == "references") { $v = explode(" ", $v); echo "\n"; ?>
		$this->has_one("<?= $v[0]; ?>", "<?= $column; ?>");<? } } } ?><? foreach ($relations as $relation) { echo "\n"; ?>
		$this->has_many("<?= $relation['model']; ?>", "<?= $relation['field']; ?>"<? if (!empty($relation['lookup'])) { ?>, "<?= $relation['lookup']; ?>", "<?= $relation['ref_field']; ?>"<? } ?>);<? } echo "\n"; ?>
	}
	
	function create($<?= $singular; ?>) {
		$this->store($<?= $singular; ?>);
	}

	function delete($<?= $singular; ?>) {
		return $this->store(array('statuses' => "deleted",  'id' => $<?= $singular; ?>['id']));
	}
	
	function query_admin($query, &$ops) {
		$query->condition("<?= $name; ?>.statuses", "deleted", "!=");
		return $query;
	}
	
	function display_admin($display, $ops) {
		$display->add("id");
	}
	
	function query_form($query, &$ops) {
		if (empty($ops['action'])) $ops['action'] = "create";
		$query->action($ops['action']);
		$query->condition($query->model.".id", $ops['id']);
		$query->select("*", $query->model);
<?php
		$tabs = "\t\t";
		foreach ($fields as $fieldname => $field) {
				if (sb()->db->has($field['type']) || $field['type'] == "category") {
					if (empty($field['column'])) $field['column'] = "id";
					echo $tabs.'$query->select($query->model.".'.$fieldname.'.'.$field['column'].' as '.$fieldname.'");'."\n";
				}
		}
		?>
		return $query;
	}
	
	function display_form($display, &$ops) {
<?php
			$tabs = "\t\t";
			foreach ($fields as $fieldname => $field) {
				if ($field['display'] === true) {
					echo $tabs.'$display->add("'.$fieldname.'");'."\n";
					if (!empty($field['confirm'])) echo $tabs.'$display->add("'.$field['confirm'].'  input_type:'.$field['input_type'].'");'."\n";
				}
			}
		?>
	}
	
	function query_get($query, &$ops) {
		return $query;
	}
	
	function query_select($query, &$ops) {
		if (!empty($ops['id'])) {
			$query->condition($query->model.".id", explode(",", $ops['id']));
		} else {
			$query->condition("<?= $name; ?>.statuses", "deleted", "!=");
		}
		$query->select("<?= $name; ?>.id");
		$query->select("<?= efault($label_select, $name.".id"); ?> as label");
		return $query;
	}

	function query_terms($query, &$ops) {
		$query = query("terms");
		$query->select("terms.id,terms.term as label");
		if (!empty($ops['taxonomy'])) $query->condition("taxonomy", $ops['taxonomy']);
		if (!empty($ops['id'])) {
			$query->join("terms_index");
			$query->condition("terms_index.type", "<?= $name; ?>");
			$query->condition("terms_index.type_id", $ops['id']);
		}
		$query->sort("terms.parent ASC, terms.term ASC");
		return $query;
	}

	function query_filters($action, $query, &$ops) {
		return $query;
	}

}
<?= '?>'; ?>
