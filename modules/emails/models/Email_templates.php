<?php
/**
 * email_templates model
 * @ingroup models
 */
namespace Starbug\Emails;
class Email_templates extends \Starbug\Core\Email_templatesModel {

	function create($email_template) {
		$this->store($email_template);
	}

}
?>
