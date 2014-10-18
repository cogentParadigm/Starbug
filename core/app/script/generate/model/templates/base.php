<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model base
 * @ingroup models
 */
class <?= ucwords($name); ?>Model extends Table {

  var $base = "<? echo $base; ?>";

	var $hooks = array(<? $count = 0; foreach ($fields as $column => $field) { if (!empty($field)) { $fcount = 0; if ($count > 0) echo ','; $count++; echo "\n"; ?>
		"<?= $column; ?>" => array(<? foreach ($field as $k => $v) { ?><? if ($fcount > 0) echo ", "; $fcount++ ?>"<?= $k; ?>" => "<?= $v; ?>"<? } ?>)<? } } echo "\n"; ?>
	);

	function init() {<? foreach ($fields as $column => $field) { foreach ($field as $k => $v) { if ($k == "references") { $v = explode(" ", $v); echo "\n"; ?>
	  $this->has_one("<?= $v[0]; ?>", "<?= $column; ?>");<? } } } ?><? foreach ($relations as $relation) { echo "\n"; ?>
		$this->has_<?php echo $relation['type']; ?>("<?= $relation['model']; ?>", "<?= $relation['field']; ?>"<?php if ($relation['type'] == "one" && !empty($relation['ref_field'])) { ?>, "<?php echo $relation['ref_field']; ?>"<?php } ?><? if (!empty($relation['lookup'])) { ?>, "<?= $relation['lookup']; ?>", "<?= $relation['ref_field']; ?>"<? } ?>);<? } echo "\n"; ?>
	}

	function create($<?= $singular; ?>) {
    <?php if (!empty($base)) { ?>
      entity_save("<?= $model; ?>", $<?= $singular; ?> + array("type" => $this->type));
    <?php } else { ?>
		  $this->store($<?= $singular; ?>);
    <?php } ?>
	}

	function delete($<?= $singular; ?>) {
    <?php if (!empty($base)) { ?>
      entity_delete("<?= $model; ?>", $<?= $singular; ?>["id"]);
    <?php } else { ?>
      remove("<?= $model; ?>", array("id" => $<?= $singular; ?>["id"]));
    <?php } ?>
	}

	function query_admin($query, &$ops) {
    <?php if (!empty($base)) { ?>
      $query = sb($this->base)->query_admin($query, $ops);
    <?php } else { ?>
      if (!logged_in("admin") && !logged_in("root")) $query->action("read");
    <?php } ?>
    return $query;
	}

	function display_admin($display, $ops) {
		<?php if (!empty($base)) { ?>
    sb($this->base)->display_admin($display, $ops);
    <?php } else { ?>
      $display->add("id");
    <?php } ?>
	}

	function query_form($query, &$ops) {
		if (empty($ops['action'])) $ops['action'] = "create";
		$query->action($ops['action']);
		$query->condition("<?= $model; ?>.id", $ops['id']);
<?php
    if (!empty($base)) foreach(array("id", $base."_id") as $idx) unset($fields[$idx]);
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
      if (!empty($base)) echo $tabs.'sb("'.$base.'")->display_form($display, $ops);'."\n";
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
