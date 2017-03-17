<?php
namespace Starbug\Db\Query;

class Parser implements ParserInterface {
	/**
	 * Parse a list of tables to be joined together.
	 *
	 * Use a comma (,) delimiter to denote an inner join
	 * - 'users,pages' => 'FROM users INNER JOIN pages'
	 *
	 * Use a less-than symbol (<) to denote a left join
	 * - 'users<pages' => 'FROM users LEFT JOIN pages'
	 *
	 * Use a greater-than symbol (>) to denote a right join
	 * - 'users>pages' => 'FROM users RIGHT JOIN pages'
	 *
	 * @param string $tables the list of tables
	 * @return array of parsed table information
	 */
	function parseTables($tables) {
		//split by one or more of: ,<>
		//capture delimiters so "users<pages,comments" becomes ["users", "<", "pages", ",", "comments"]
		$tables = preg_split('/([,\<\>]+)/', $tables, -1, PREG_SPLIT_DELIM_CAPTURE);
		$results = array($this->parseName($tables[0]));
		$count = count($tables);
		for ($i = 2; $i < $count; $i += 2) {
			$table = $tables[$i];
			$type = str_replace(array(",", "<>", "<", ">"), array("INNER", "OUTER", "LEFT", "RIGHT"), trim($tables[$i-1]));
			$results[] = array_merge($this->parseName($table), array("join" => $type));
		}
		return $results;
	}

	/**
	 * Parse an expression containing a table name and associated clauses
	 * 'users as people on people.id=pages.owner' becomes:
	 * [
	 * 	"name" => "users",
	 * 	"alias" => "people",
	 * 	"on" => "people.id=pages.owner"
	 * ]
	 */
	function parseName($name) {
		$parts = explode(' ', $name);
		$on = $join = "";
		$alias = false;
		$count = count($parts);
		if ($count > 2 && strtolower($parts[$count-2]) == "on") {
			$on = array_pop($parts);
			array_pop($parts);
			$count -= 2;
			$alias = implode(' ', $parts);
		}
		if ($count > 2 && strtolower($parts[$count-2]) == "as") {
			$alias = array_pop($parts);
			array_pop($parts);
			$count -= 2;
		}
		$name = implode(' ', $parts);
		return array("name" => $name, "alias" => $alias, "on" => $on);
	}

	/**
	 * Parse an expression that contains one or more column references
	 * Examples:
	 *
	 * "pages.title,pages.owner.first_name"                          => "`pages`.title,`pages_owner`.first_name"
	 *
	 * "CONCAT(pages.owner.first_name, ' ' pages.owner.last_name)"   =>  "CONCAT(`pages_owner`.first_name, ' ', `pages_owner`.last_name)"
	 *
	 * "pages.owner.first_name=pages.owner.last_name"                =>  "`pages_owner`.first_name=`pages_owner`.last_name"
	 */
	function parseColumns($fields, $mode = "select") {
		preg_match_all('/[a-zA-Z_\.\*]+/', $fields, $matches, PREG_OFFSET_CAPTURE);
		$offset = 0;
		foreach ($matches[0] as $match) {
			if (false !== strpos($match[0], ".")) {
				$prefix = substr($fields, 0, $match[1]+$offset);
				if (((substr_count($prefix, "'") - substr_count($prefix, "\\'")) % 2 == 0) && ((substr_count($prefix, '"') - substr_count($prefix, '\\"')) % 2 == 0)) {
					$replacement = $this->parseColumn($match[0], $mode);
					$source_length = strlen($match[0]);
					$replacement_length = strlen($replacement);
					$fields = substr_replace($fields, $replacement, $match[1]+$offset, $source_length);
					$offset += $replacement_length - $source_length;
				}
			}
		}
		return $fields;
	}

	/**
	 * Parse an expression referencing a column name
	 *
	 * 'pages.owner.first_name'  =>  '`pages_owner`.first_name'
	 *
	 */
	function parseColumn(QueryInterface $query, $field) {
		$column = [];
		//split the field into parts
		$parts = explode(".", $field);
		$count = count($parts);
		//we only proceed if there is more than one token, meaning this field name has a '.'
		if ($count > 1) {
			//the first token is either a table alias or the name of a column that references another table
			//if it's a column, then we'll assume it's a column of our base table
			$alias = $query->getAlias();
			//if it's a collection, we'll use it
			if ($query->hasTable($parts[0])) {
				$alias = array_shift($parts);
			}
			//now we start a loop to process the remaining tokens
			while (!empty($parts)) {
				//shift off the first token
				$token = array_shift($parts);
				//if there are no more tokens, then this is the final column name
				if (empty($parts)) {
					//get the field string and table name
					$field = "`".$alias."`.".$token;
					if ($query->hasTable($alias)) {
						//otherwise we look at the field schema if we recognize the table
						$table = $query->getTable($alias)->getName();
						if ($this->schema->hasColumn($table, $token)) {
							$schema = $this->schema->getColumn($table, $token);
							if ($schema['entity'] !== $table) {
								$entity_alias = $alias."_".$schema['entity'];
								if (!$this->query->has($entity_alias)) {
									$base_entity = $this->schema->getEntityRoot($table);
									$entity_condition = ($schema["entity"] === $base_entity) ? $entity_alias.".id=".$alias.".".$base_entity."_id" : $entity_alias.".".$base_entity."_id=".$alias.".".$base_entity."_id";
									$query->addJoin($schema["entity"], $entity_alias)->on($entity_condition);
								}
								$field = "`".$entity_alias."`.".$token;
								$alias = $entity_alias;
							}
						}
					}
				} else {
					//otherwise we need to join this reference, and pass along the next token
					$result = $this->with($token, $alias, $mode, $parts[0]);
					if ($result) {
						//$this->with may return a sub query instead of performing a join. If it does, that means we're done
						//and we can end the loop. In the case of a condition, it also means that the operands should be inverted
						$field = $result;
						$parts = array();
						$invert = true;
					}
					//if the loop continues, the current token becomes the next alias
					$column_info = $this->models->get($this->query['from'][$alias])->column_info($token);
					if (!empty($column_info) && $column_info['entity'] !== $this->query['from'][$alias]) {
						$table = $column_info['entity'];
						$alias = $alias."_".$column_info['entity'];
					}
					$alias = isset($this->query['from'][$alias.'_'.$token]) ? $alias.'_'.$token : $token;
				}
			}
		}
		return $column;
	}
	function parseColumn($field, $mode = "select") {
		//split the field into parts
		$parts = explode(".", $field);
		$count = count($parts);
		//invert indicates that the operands should be flipped. eg 'some value' NOT IN (..sub query..)
		//in the example above the value is on the left
		$invert = false;
		//a condition may be added to a set other than the default where clause
		$set = false;
		$ornull = false;
		//we only proceed if there is more than one token, meaning this field name has a '.'
		if ($count > 1) {
			//the first token is either a table alias or the name of a column that references another collection
			//if it's a column, then we'll assume it's a column of our base collection
			$alias = $this->base_collection;
			//if it's a collection, we'll use it
			if (!empty($this->query['from'][$parts[0]])) {
				$alias = array_shift($parts);
			}
			//now we start a loop to process the remaining tokens
			while (!empty($parts)) {
				//shift off the first token
				$token = array_shift($parts);
				//if there are no more tokens, then this is the final column name
				if (empty($parts)) {
					//get the field string and table name
					$field = "`".$alias."`.".$token;
					$table = $this->query['from'][$alias];
					if (!empty($table)) {
						//otherwise we look at the field schema if we recognize the table
						if ($this->schema->hasColumn($table, $token)) {
							$schema = $this->schema->getColumn($table, $token);
							if ($schema['entity'] !== $table) {
								$entity_alias = $alias."_".$schema['entity'];
								if (!isset($this->query['from'][$entity_alias])) {
									$base_entity = $this->schema->getEntityRoot($table);
									$this->join($schema['entity']." as ".$entity_alias);
									if ($schema['entity'] === $base_entity) $this->on($entity_alias.".id=".$alias.".".$base_entity."_id");
									else $this->on($entity_alias.".".$base_entity."_id=".$alias.".".$base_entity."_id");
								}
								$field = "`".$entity_alias."`.".$token;
								$alias = $entity_alias;
							}
						}
					}
				} else {
					//otherwise we need to join this reference, and pass along the next token
					$result = $this->with($token, $alias, $mode, $parts[0]);
					if ($result) {
						//$this->with may return a sub query instead of performing a join. If it does, that means we're done
						//and we can end the loop. In the case of a condition, it also means that the operands should be inverted
						$field = $result;
						$parts = array();
						$invert = true;
					}
					//if the loop continues, the current token becomes the next alias
					$column_info = $this->models->get($this->query['from'][$alias])->column_info($token);
					if (!empty($column_info) && $column_info['entity'] !== $this->query['from'][$alias]) {
						$table = $column_info['entity'];
						$alias = $alias."_".$column_info['entity'];
					}
					$alias = isset($this->query['from'][$alias.'_'.$token]) ? $alias.'_'.$token : $token;
				}
			}
		}
		if ($mode == "condition") {
			$field = array("field" => $field, "invert" => $invert);
			if (false !== $set) $field["set"] = $set;
			if (false !== $ornull) $field["ornull"] = true;
		}
		return $field;
	}
}
