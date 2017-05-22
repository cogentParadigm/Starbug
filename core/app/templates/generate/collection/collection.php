<?php echo '<?php'."\n"; ?>
namespace Starbug\App;

use Starbug\Core\AdminCollection;

class <?php echo ucwords($name); ?>Collection extends AdminCollection {
  public function build($query, &$ops) {
    return parent::build($query, $ops);
  }
}