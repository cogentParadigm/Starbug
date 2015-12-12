<?php echo '<?php'."\n"; ?>
class <?php echo ucwords($model); ?>Grid extends GridDisplay {
	public $model = "<?php echo $model; ?>";
	public $action = "admin";
	function build_display($options) {
<?php foreach ($generator->fields as $name => $field) { ?>		$this->add("<?php echo $name; ?>");
<?php } ?>
	}
}
<?php echo '?>'; ?>
