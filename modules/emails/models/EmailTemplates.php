<?php
namespace Starbug\Emails;
class EmailTemplates extends \Starbug\Core\EmailTemplatesModel {
	function create($email_template) {
		$this->store($email_template);
	}
}
