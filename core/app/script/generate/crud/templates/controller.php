<?= '<?php'."\n"; ?>
class Admin<?= ucwords($model); ?>Controller {
	function init() {
		$this->assign("model", "<?= $model; ?>");
		$this->assign("cancel_url", "admin/<?= $model; ?>");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("<?= $model; ?>", "create")) redirect(uri("admin/<?= $model; ?>", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		$this->assign("id", $id);
		if (success("<?= $model; ?>", "create")) redirect(uri("admin/<?= $model; ?>", 'u'));
		else $this->render("admin/update");
	}
}
<?= '?>'; ?>
