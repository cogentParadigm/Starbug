<?php
namespace Starbug\Core\Generator\Definitions;
use Starbug\Core\Generator\Definition;
class Crud extends Definition {
	public function build($options = []) {
		parent::build($options);
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $options["model"])));
		$this->setParameter("className", $className);
		$this->addTemplate(
			"generate/crud/controller",
				$this->module."/controllers/Admin".$className."Controller.php"
		);
		$this->addTemplate(
			"generate/crud/api",
				$this->module."/controllers/Api".$className."Controller.php"
		);
	}
}
?>
