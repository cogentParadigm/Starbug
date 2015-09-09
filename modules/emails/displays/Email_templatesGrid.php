<?php
namespace Starbug\Emails;
use Starbug\Core\GridDisplay;
class Email_templatesGrid extends GridDisplay {
	public $model = "email_templates";
	public $action = "admin";
	function build_display($options) {
		$this->add("name");
	}
}
?>
