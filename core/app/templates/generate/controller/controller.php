<?php echo '<?php'."\n"; ?>
namespace Starbug\App;

use Starbug\Core\Controller;

/**
 * <?php echo ucwords($name); ?> Controller.
 */
class <?php echo ucwords($name); ?>Controller extends Controller {
  function init() {
    //perform any common startup here
  }
  function default_action() {
    $this->render("<?php echo $name; ?>/default");
  }
}
