<?php echo '<?php'."\n"; ?>
class Admin<?php echo ucwords($model); ?>Controller {
	function init() {
		$this->assign("model", "<?php echo $model; ?>");
		$this->assign("cancel_url", "admin/<?php echo $model; ?>");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("<?php echo $model; ?>", "create")) redirect(uri("admin/<?php echo $model; ?>", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		$this->assign("id", $id);
		if (success("<?php echo $model; ?>", "create")) redirect(uri("admin/<?php echo $model; ?>", 'u'));
		else $this->render("admin/update");
	}
}
<?php echo '?>'; ?>
