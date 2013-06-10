<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file modules/emails/up.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup migrations
 */
/**
 * emails module migration
 * @ingroup migrations
 */
$this->table("email_templates",
	"name  type:string  length:128  list:true",
	"subject  type:string  length:155",
	"from  type:string  length:255  default:",
	"from_name  type:string  length:128  default:",
	"cc  type:text  default:",
	"bcc  type:text  default:",
	"body  type:text  class:rich-text"
);
$this->store(
	"email_templates",
	"name:Registration",
	array(
		"subject" => "Welcome to [site:name]!",
		"body" => "<h2>Welcome to [site:name]!</h2>\n<p>You can login using this email address ([user:email]) at <a href=\"[url:login]\">[url:login]</a></p>"
	)
);
$this->store(
	"email_templates",
	"name:Account Creation",
	array(
		"subject" => "Welcome to [site:name]!",
		"body" => "<h2>Welcome to [site:name]!</h2>\n<p>An account has been created for you. You can login at <a href=\"[url:login]\">[url:login]</a>.</p><p>Here are your credentials.<br/>login: [user:email]<br/>password: [user:password]</p>"
	)
);
$this->store("menus", "menu:admin  href:admin/emails", "content:Email Templates  parent:1  position:5");
?>
