<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/Macro.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
/**
 * an implementation of MacroInteface
 */
class Macro implements MacroInterface {

	private $hook_builder;

	public function __construct(HookFactoryInterface $hooks) {
		$this->hook_builder = $hooks;
	}

	/**
	 * Matches tokens with the following pattern: [$type:$name]
	 * $type and $name may not contain  [ ] characters.
	 * $type may not contain : or whitespace characters, but $name may.
	 * @ingroup strings
	 * @param string $text text content to parse for tokens
	 * @return array an array of tokens
	 */
	public function search($text) {
		preg_match_all('/
			\[             # [ - pattern start
			([^\s\[\]:]*)  # match $type not containing whitespace : [ or ]
			:              # : - separator
			([^\[\]]*)     # match $name not containing [ or ]
			\]             # ] - pattern end
			/x', $text, $matches);

		$types = $matches[1];
		$tokens = $matches[2];

		// Iterate through the matches, building an associative array containing
		// $tokens grouped by $types, pointing to the version of the token found in
		// the source text. For example, $results['user']['email'] = '[user:email]';
		$results = array();
	 for ($i = 0; $i < count($tokens); $i++) {
		 $results[$types[$i]][$tokens[$i]] = $matches[0][$i];
	 }

		return $results;
	}

	/**
	 * Uses the token_search function to extract tokens from the text, and token_replacements to find replacements
	 * The provided data can be used to override values
	 * For example, to replace the token [user:email], pass array("user" => array("email" => "john@doe.com"));
	 * site tokens will be replaced automatically from the site settings
	 * @ingroup strings
	 * @param string $text text content to parse for tokens
	 * @return array an array of tokens
	 */
	public function replace($text, $data = array()) {
		//find out what tokens we need to replace
		$tokens = $this->search($text);
		if (empty($tokens)) return $text;

		//get the replacements from token_replacements
		$replacements = array();
		foreach ($tokens as $type => $type_tokens) $replacements += $this->replacements($type, $type_tokens, $data);

		//replace tokens
		$search = array_keys($replacements);
		$replace = array_values($replacements);
		return str_replace($search, $replace, $text);
	}

	/**
	 * provides token replacements
	 * @ingroup strings
	 * @param string $type the type of token. For example, in '[user:email]' the type is 'user'.
	 * @param array $tokens the tokens you need replacements for (in the format returned by token_search).
	 * @param array $data (optional) data to override replacements or pass to token providers
	 * @return array an associative array of replacements
	 */
	private function replacements($type, $tokens, $data = array()) {
		$replacements = array();
		//gather replacements
		if (!isset($this->hooks[$type])) {
			$this->hooks[$type] = $this->hook_builder->get("macro/".$type);
		}
		foreach ($this->hooks[$type] as $hook) {
			foreach ($tokens as $name => $token) {
				$replacements[$token] = $hook->replace($this, $name, $token, $data);
			}
		}
		//populate overrides from data
	 if (!empty($data[$type]) && is_array($data[$type])) {
	  foreach ($tokens as $index => $token) {
		  if (!empty($data[$type][$index])) $replacements[$token] = $data[$type][$index];
	  }
	 }
		//return replacements
		return $replacements;
	}
}
