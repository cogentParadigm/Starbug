<?php
namespace Starbug\Core\Generator\Definitions;
use Starbug\Core\Generator\Definition;
class Crud extends Definition {
	public function build($options = []) {
		parent::build($options);
		$this->addTemplate(
			"generate/crud/controller",
				$this->module."/controllers/Admin".ucwords($options["model"])."Controller.php"
		);
		$this->addTemplate(
			"generate/crud/api",
				$this->module."/controllers/Api".ucwords($options["model"])."Controller.php"
		);
	}
}
?>
