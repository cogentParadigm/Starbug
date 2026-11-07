<?php
namespace Starbug\Content;

use Starbug\Macro\MacroInterface;
use Starbug\Core\Display;
use Starbug\Templates\TemplateInterface;

class BlockDisplay extends Display {
  public $template = "block.html";
  protected $macro;
  public function __construct(TemplateInterface $output, MacroInterface $macro) {
    $this->output = $output;
    $this->macro = $macro;
  }
  public function buildDisplay($options) {
    $this->options["block"]["content"] = $this->macro->replace($this->options["block"]["content"]);
  }
}
