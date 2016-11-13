<?php
namespace Starbug\Core\Generator;
use Starbug\Core\TemplateInterface;
class Generator {
	public function __construct(TemplateInterface $renderer, $base_directory = false) {
		$this->renderer = $renderer;
		$this->base_directory = $base_directory;
		if (false == $this->base_directory) {
			$this->base_directory = getcwd();
		}
	}
	public function generate(Definition $definition, $options = []) {
		$definition->build($options);
		//CREATE DIRECTORIES
		foreach ($definition->getDirectories() as $dir) {
			if (!file_exists($this->base_directory."/".$dir)) passthru("mkdir ".$this->base_directory."/".$dir);
		}
		//CREATE FILES
		foreach ($definition->getTemplates() as $template => $output) {
			$data = $this->renderer->capture($template, $definition->getParameters());
			file_put_contents($this->base_directory."/".$output, $data);
		}
		//COPY FILES
		foreach ($definition->getCopies() as $origin => $dest) {
			passthru("cp ".$this->base_directory."/$origin ".$this->base_directory."/".$dest);
		}
	}
}
?>
