<?php
namespace Starbug\Core;
class hook_macro_url extends MacroHook {
	protected $url;
	public function __construct(URLInterface $url) {
		$this->url = $url;
	}
	public function replace($macro, $name, $token, $data) {
		return isset($data['absolute_urls']) ? $this->url->build($name, $data['absolute_urls']) : $this->url->build($name);
	}
}
?>
