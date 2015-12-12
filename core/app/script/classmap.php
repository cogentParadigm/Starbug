<?php
namespace Starbug\Core;
class ClassmapCommand {
	protected $config;
	protected $base_directory;
	protected $modules;
	public function __construct(ConfigInterface $config, $base_directory, $modules) {
		$this->config = $config;
		$this->base_directory = $base_directory;
		$this->modules = $modules;
	}
	public function run($args) {
		$classes = $this->config->get("autoload", "factory");
		foreach ($this->modules as $mid => $target) {
			$classes = array_merge($classes, $this->get_classes($target, $this->base_directory));
		}
		file_put_contents($this->base_directory."/var/autoload_classmap.php", $this->format($classes));
	}
	protected function get_classes($directory, $base) {
		$classes = array();
		foreach (scandir($base."/".$directory) as $file) {
			if (is_dir($base."/".$directory."/".$file)) {
				if (substr($file, 0, 1) !== ".") $classes = array_merge($classes, $this->get_classes($directory."/".$file, $base));
			} else if (substr($file, -4) === ".php") {
				$class = substr($file, 0, -4);
				$data = file_get_contents($base."/".$directory."/".$file);
				if (false !== strpos($data, 'class '.$class) || false !== strpos($data, 'interface '.$class)) {
					$classes["Starbug\Core\\".$class] = $directory."/".$file;
				}
			}
		}
		return $classes;
	}
	protected function format($map) {
		$raw = print_r($map, true);
		$search = array('[', '] => ', ".php\n", "Array\n(", ",\n)\n;");
		$replace = array('"', '" => "', '.php",'."\n", "return array(", "\n);");
		return "<?php\n".str_replace($search, $replace, $raw.";\n?>");
	}
}
?>
