<?php
namespace Starbug\Files\Admin\Display;

use Starbug\Display\FormDisplay;

class FilesForm extends FormDisplay {
  public $model = "files";
  public function buildDisplay($options) {
    $this->add("caption");
  }
}
