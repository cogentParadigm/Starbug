<?php
namespace Starbug\Files\Display;

use Starbug\Core\FormDisplay;

class FilesForm extends FormDisplay {
  public $model = "files";
  public function buildDisplay($options) {
    $this->add("caption");
  }
}
