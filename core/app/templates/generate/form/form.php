<?php echo '<?php'."\n"; ?>

namespace Starbug\App;

use Starbug\Core\FormDisplay;

class <?php echo $className; ?>Form extends FormDisplay {
  public $model = "<?php echo $model; ?>";
  public $cancel_url = "admin/<?php echo $model; ?>";
  function build_display($options) {
<?php foreach ($fields as $name => $field) { ?>    $this->add("<?php echo $name; ?>");
<?php } ?>
  }
}
<?php echo '?>'; ?>
