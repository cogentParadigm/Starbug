<?php
namespace Starbug\Core;
/**
 * a simple interface for parsing and replacing macro tokens
 */
interface MacroInterface {
	public function search($text);
	public function replace($text, $data = array());
}
