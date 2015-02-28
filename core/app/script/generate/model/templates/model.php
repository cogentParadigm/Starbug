<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model
 * @ingroup models
 */
class <?= ucwords($name); ?> {

	function create($<?= $singular; ?>) {
		$this->store($<?= $singular; ?>);
	}

  /******************************************************************
	 * Query functions
	 *****************************************************************/

  function query_admin($query, &$ops) {
    $query = parent::query_admin($query, $ops);
    return $query;
  }

  function query_filters($action, $query, $ops) {
      if (!logged_in("root") && !logged_in("admin")) $query->action("read");
      return $query;
  }

	/******************************************************************
	 * Display functions
	 *****************************************************************/

	function display_admin($display, $ops) {
	<?php if (!empty($base)) { ?>
    sb($this->base)->display_admin($display, $ops);
  <?php } else { ?>
    $display->add("id");
  <?php } ?>
	}

}
<?= '?>'; ?>
