<?php
namespace Starbug\Content;
use Starbug\Core\Display;
use Starbug\Core\TemplateInterface;
use Starbug\Core\MacroInterface;
class BlockDisplay extends Display {
	public $template = "block";
	protected $macro;
	public function __construct(TemplateInterface $output, MacroInterface $macro) {
		$this->output = $output;
		$this->macro = $macro;
	}
	function build_display($options) {
		$this->output->assign("macro", $this->macro);
	}
}
