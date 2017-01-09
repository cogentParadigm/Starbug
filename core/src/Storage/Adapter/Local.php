<?php
namespace Starbug\Core\Storage\Adapter;
use Starbug\Core\Storage\AdapterInterface;
use Starbug\Core\URLInterface;
use League\Flysystem\Adapter\Local as ParentAdapter;
class Local extends ParentAdapter implements AdapterInterface {
	protected $url;
	public function setURLInterface(URLInterface $url) {
		$this->url = $url;
	}
	public function getURL($path, $absolute = false) {
		return $this->url->build($path, $absolute);
	}
}
