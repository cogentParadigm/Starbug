<?php
namespace Starbug\Core\Storage;
use League\Flysystem\AdapterInterface as ParentInterface;
interface AdapterInterface extends ParentInterface {
	public function getURL($path);
}
