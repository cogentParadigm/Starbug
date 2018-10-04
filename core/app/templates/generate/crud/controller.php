<?php echo '<?php'."\n"; ?>

namespace Starbug\App;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class Admin<?php echo $className; ?>Controller extends Controller {
  public $routes = array(
    'update' => '{id}'
  );
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "<?php echo $model; ?>");
    $this->assign("cancel_url", "admin/<?php echo $model; ?>");
  }
  function default_action() {
    $this->render("admin/list");
  }
  function create() {
    if ($this->db->success("<?php echo $model; ?>", "create")) $this->redirect("admin/<?php echo $model; ?>");
    else $this->render("admin/create");
  }
  function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("<?php echo $model; ?>", "create")) $this->redirect("admin/<?php echo $model; ?>");
    else $this->render("admin/update");
  }
}
<?php echo '?>'; ?>
