<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/interface/DisplayFactoryInterface.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
/**
* hook factory interface
*/
interface HookFactoryInterface {
	public function get($hook);
}
