<?php
namespace Starbug\Users\Admin\Display;

use Starbug\Db\CollectionFactoryInterface;
use Starbug\Dojo\Display\GridDisplay;
use Starbug\Dojo\Service\DojoConfiguration;
use Starbug\Modules\Configuration;
use Starbug\Templates\TemplateInterface;

class UsersGrid extends GridDisplay {
  public $model = "users";
  public $action = "admin";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    DojoConfiguration $dojo,
    protected Configuration $modules
  ) {
    parent::__construct($output, $collections, $dojo);
  }
  public function buildDisplay($options) {
    $this->add("first_name", "last_name", "email", "last_visit", "groups", ["deleted", "label" => "Status"]);
    if ($this->modules->isEnabled("impersonate")) {
      $this->update([
        "row_options",
        "label" => "Options",
        "buttons" => "[".
          "{".
            "url: dojoConfig.websiteUrl + 'admin/users/impersonate/\${id}', ".
            "title: 'Impersonate', ".
            "icon: 'fa-sign-in'".
          "}".
        "]"
      ]);
    }
  }
}
