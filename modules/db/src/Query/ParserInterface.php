<?php
namespace Starbug\Db\Query;
interface ParserInterface {
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
	public function parseTables($tables);
	/**
	 * Parse an expression containing a table name and associated clauses
	 * 'users as people on people.id=pages.owner' becomes:
	 * [
	 * 	"collection" => "users",
	 * 	"alias" => "people",
	 * 	"on" => "people.id=pages.owner"
	 * ]
	 */
	public function parseName($name);
}
