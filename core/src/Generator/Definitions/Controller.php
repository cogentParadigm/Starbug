<?php
namespace Starbug\Core\Generator\Definitions;
use Starbug\Core\Generator\Definition;
class Controller extends Definition {
	public function build($options = []) {
		parent::build($options);
		$this->addTemplate(
			"generate/controller/controller",
				$this->module."/controllers/".ucwords($options["name"])."Controller.php"
		);
	}
}
