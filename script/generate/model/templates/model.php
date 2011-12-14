<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model
 * @ingroup models
 */
class <?= ucwords($name); ?> extends <?= ucwords($name); ?>Model {

	function create($<?= $singular; ?>) {
		$this->store($<?= $singular; ?>);
	}

	function delete($<?= $singular; ?>) {
		return $this->remove('id='.$<?= $singular; ?>['id']);
	}

}
<?= '?>'; ?>
