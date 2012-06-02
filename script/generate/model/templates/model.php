<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model
 * @ingroup models
 */
class <?= ucwords($name); ?> {

	function create($<?= $singular; ?>) {
		$this->store($<?= $singular; ?>);
	}

}
<?= '?>'; ?>
