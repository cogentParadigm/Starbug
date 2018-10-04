<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\QueryInterface;

trait Parsing {
	/**
	 * Parse an expression containing a table name and associated clauses
	 * 'users as people on people.id=pages.owner' becomes:
	 * [
	 * 	"name" => "users",
	 * 	"alias" => "people",
	 * 	"on" => "people.id=pages.owner"
	 * ]
	 */
	protected function parseName($name) {
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
	function parseColumns(QueryInterface $query, $fields) {
		preg_match_all('/[a-zA-Z_\.\*]+/', $fields, $matches, PREG_OFFSET_CAPTURE);
		$offset = 0;
		foreach ($matches[0] as $match) {
			if (false !== strpos($match[0], ".")) {
				$prefix = substr($fields, 0, $match[1]+$offset);
				if (((substr_count($prefix, "'") - substr_count($prefix, "\\'")) % 2 == 0) && ((substr_count($prefix, '"') - substr_count($prefix, '\\"')) % 2 == 0)) {
					$replacement = $this->parseColumn($query, $match[0]);
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
	 * Expand a reference to another table.
	 * examples: pages.owner, pages.comments, pages.comments.owner
	 */
	public function parseColumn(QueryInterface $query, $field) {
		//split the column into parts
		$parts = explode(".", $field);
		if (count($parts) > 1) {
			$token = array_pop($parts);
			$alias = $this->expand($query, implode(".", $parts), true);
			//get the field string and table name
			$field = "`".$alias."`.".$token;
			$table = $query->getTable($alias);
			if (!empty($table)) {
				//otherwise we look at the field schema if we recognize the table
				$table = $table->getName();
				if ($this->schema->hasColumn($table, $token)) {
					$schema = $this->schema->getColumn($table, $token);
					if ($schema['entity'] !== $table) {
						$entity_alias = $alias."_".$schema['entity'];
						if (!$query->hasTable($entity_alias)) {
							$base_entity = $this->schema->getEntityRoot($table);
							$join = $query->addJoin($schema["entity"], $entity_alias);
							if ($schema['entity'] === $base_entity) $join->where($entity_alias.".id=".$alias.".".$base_entity."_id");
							else $join->where($entity_alias.".".$base_entity."_id=".$alias.".".$base_entity."_id");
						}
						$field = "`".$entity_alias."`.".$token;
						$alias = $entity_alias;
					}
				}
			}
		}
		return $field;
	}

	/**
	 * Expand a reference to another table.
	 * examples: pages.owner, pages.comments, pages.comments.owner
	 */
	public function expand(QueryInterface $query, $column, $returnAlias = false) {
		//split the column into parts
		$parts = explode(".", $column);
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
			//parse and join this reference
			$parsed = $this->parseName($token);
			$table = $query->getTable($alias)->getName();
			$schema = $this->schema->getColumn($table, $parsed["name"]);
			if (empty($schema)) return $returnAlias ? $column : $this;
			if ($schema["entity"] !== $table) {
				$parentAlias = $alias."_".$schema["entity"];
				if (!$query->hasTable($parentAlias) && $this->schema->hasTable($table)) {
					$root = $this->schema->getEntityRoot($table);
					$join = $query->addJoin($schema["entity"], $parentAlias);
					if ($schema["entity"] == $root) $join->on($parentAlias.".id=".$alias.".".$root."_id");
					else $join->on($parentAlias.".".$root."_id=".$alias.".".$root."_id");
				}
				$table = $schema["entity"];
				$alias = $parentAlias;
			}
			$nextAlias = (empty($parts) && $parsed["alias"]) ? $parsed["alias"] : $alias."_".$parsed["name"];
			if (!$query->hasTable($nextAlias)) {
				if (!empty($schema["references"])) {
					$ref = explode(" ", $schema["references"]);
					$query->addJoinOne($alias.".".$parsed["name"], $ref[0], $nextAlias);
				} elseif ($this->schema->hasTable($schema["type"])) {
					if ($schema["table"] == $schema["type"]) {
						$query->addJoinMany($alias, $schema["type"], $nextAlias);
					} else {
						if (empty($schema["table"])) $schema["table"] = $schema["entity"]."_".$parsed["name"];
						$query->addJoinMany($alias, $schema["table"], $nextAlias."_lookup");
						$query->addJoinOne($nextAlias."_lookup.".$parsed["name"]."_id", $schema["type"], $nextAlias);
					}
				}
			}
			$alias = $nextAlias;
		}
		return $returnAlias ? $alias : $this;
	}

}
