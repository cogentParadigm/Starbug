<?= '<?php'."\n"; ?>
class <?= ucwords($model); ?>Controller {
	function init() {
		$this->assign("model", "<?= $model; ?>");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("<?= $model; ?>", "create")) redirect(uri("admin/<?= $model; ?>/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		$this->assign("id", $id);
		$this->render("admin/update");
	}
}
<?= '?>'; ?>
