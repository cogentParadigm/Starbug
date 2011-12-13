<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model
 * @ingroup models
 */
class <?= $label; ?> extends <?= $label; ?>Model {

	function create($<?= $singular; ?>) {
		$this->store($<?= $singular; ?>);
	}

	function delete($<?= $singular; ?>) {
		return $this->remove('id='.$<?= $singular; ?>['id']);
	}

}
<?= '?>'; ?>
