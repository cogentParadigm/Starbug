<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/remove.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
namespace Starbug\Core;
class RemoveCommand {
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	public function run($argv) {
		$name = array_shift($argv);
		$id = array_shift($argv);
		$this->models->get($name)->remove($id);
	}
}
?>
