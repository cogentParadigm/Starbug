<?php echo '<?php'."\n"; ?>

namespace Starbug\App;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class Api<?php echo $className; ?>Controller extends ApiController {
  public $model = "<?php echo $model; ?>";
  function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  function admin() {
    $this->api->render("Admin");
  }
  function select() {
    $this->api->render("Select");
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}
<?php echo '?>'; ?>
