<?php echo '<?php'."\n"; ?>
use Starbug\Core\Controller;
class <?php echo ucwords($model); ?>Controller extends Controller {
	function init() {
		//perform any common startup here
	}
	function default_action() {
		$this->render("<?php echo $model; ?>/default");
	}
}
<?php echo '?>'; ?>
