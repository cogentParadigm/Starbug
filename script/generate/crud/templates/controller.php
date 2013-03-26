<?= '<?php'."\n"; ?>
class Admin<?= ucwords($model); ?>Controller {
	function init() {
		assign("model", "<?= $model; ?>");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("<?= $model; ?>", "create")) redirect(uri("admin/<?= $model; ?>/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/update");
	}
}
<?= '?>'; ?>
