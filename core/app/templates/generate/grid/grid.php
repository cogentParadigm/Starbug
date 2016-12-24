<?php echo '<?php'."\n"; ?>
namespace Starbug\App;
use Starbug\Core\GridDisplay;
class <?php echo $className; ?>Grid extends GridDisplay {
	public $model = "<?php echo $model; ?>";
	public $action = "admin";
	function build_display($options) {
<?php foreach ($fields as $name => $field) { ?>		$this->add("<?php echo $name; ?>");
<?php } ?>
	}
}
<?php echo '?>'; ?>
