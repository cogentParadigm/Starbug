<?php echo '<?php'."\n"; ?>
class <?php echo ucwords($model); ?>Controller {
	function init() {
		//perform any common startup here
	}
	function default_action() {
		$this->render("<?php echo $model; ?>/default");
	}
}
<?php echo '?>'; ?>
