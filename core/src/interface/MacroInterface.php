<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/MacroInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
/**
 * a simple interface for parsing and replacing macro tokens
 */
interface MacroInterface {
	public function search($text);
	public function replace($text, $data = array());
}
