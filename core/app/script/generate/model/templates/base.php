<?php
  $factory = sb()->config->get("models", "factory");
  $factory = isset($factory[$name]) ? $factory[$name] : array();
  extract(schema($model));
  echo '<?php'."\n";
?>
/**
 * <?php echo $name; ?> model base
 * @ingroup models
 */
class <?php echo ucwords($name); ?>Model extends Table {

  public $type = "<?php echo $name; ?>";
  public $base = "<?php echo $base; ?>";

  function __construct(db $db<?php foreach ($factory as $n => $t) echo ', '.$t.' $'.$n; ?>) {
    $this->db = $db;<?php foreach ($factory as $n => $t) echo "\n\t\t\$this->".$n.' = $'.$n.';'; ?>

    $this->init();
  }

	public $hooks = array(<?php $count = 0; foreach ($fields as $column => $field) { if (!empty($field)) { $fcount = 0; if ($count > 0) echo ','; $count++; echo "\n"; ?>
		"<?php echo $column; ?>" => array(<?php foreach ($field as $k => $v) { ?><?php if ($fcount > 0) echo ", "; $fcount++ ?>"<?php echo $k; ?>" => "<?php echo $v; ?>"<?php } ?>)<?php } } echo "\n"; ?>
	);

	function init() {<?php foreach ($fields as $column => $field) { foreach ($field as $k => $v) { if ($k == "references") { $v = explode(" ", $v); echo "\n"; ?>
	  $this->has_one("<?php echo $v[0]; ?>", "<?php echo $column; ?>");<?php } } } ?><?php foreach ($relations as $relation) { echo "\n"; ?>
		$this->has_<?php echo $relation['type']; ?>("<?php echo $relation['model']; ?>", "<?php echo $relation['field']; ?>"<?php if ($relation['type'] == "one" && !empty($relation['ref_field'])) { ?>, "<?php echo $relation['ref_field']; ?>"<?php } ?><?php if (!empty($relation['lookup'])) { ?>, "<?php echo $relation['lookup']; ?>", "<?php echo $relation['ref_field']; ?>"<?php } ?>);<?php } echo "\n"; ?>
	}

	function create($<?php echo $singular; ?>) {
    <?php if (!empty($base)) { ?>
      entity_save("<?php echo $model; ?>", $<?php echo $singular; ?> + array("type" => $this->type));
    <?php } else { ?>
		  $this->store($<?php echo $singular; ?>);
    <?php } ?>
	}

	function delete($<?php echo $singular; ?>) {
    <?php if (!empty($base)) { ?>
      entity_delete("<?php echo $model; ?>", $<?php echo $singular; ?>["id"]);
    <?php } else { ?>
      remove("<?php echo $model; ?>", array("id" => $<?php echo $singular; ?>["id"]));
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

  function display_select($display, $ops) {
    <?php if (!empty($base)) { ?>
    sb($this->base)->display_select($display, $ops);
    <?php } else { ?>
      $display->add("id", "label");
    <?php } ?>
  }

	function query_form($query, &$ops) {
		if (empty($ops['action'])) $ops['action'] = "create";
		$query->action($ops['action'], "<?php echo $model; ?>");
		$query->condition("<?php echo $model; ?>.id", $ops['id']);
<?php
    if (!empty($base)) {
      unset($fields["id"]);
      foreach(entity_chain($base) as $b) unset($fields[$b."_id"]);
    }
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
			$query->condition("<?php echo $name; ?>.statuses.slug", "deleted", "!=", array("ornull" => true));
		}
		$query->select("<?php echo $name; ?>.id");
		$query->select("<?php echo efault($label_select, $name.".id"); ?> as label");
		return $query;
	}

	function query_filters($action, $query, &$ops) {
		return $query;
	}

}
<?php echo '?>'; ?>
