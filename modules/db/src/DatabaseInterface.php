<?php
namespace Starbug\Db;

use Starbug\Db\Query\BuilderInterface;

interface DatabaseInterface {
  /**
   * Get records or columns.
   *
   * @param string $model the name of the model
   * @param mixed $id/$conditions the id or an array of conditions
   * @param string $column optional column name
   */
  public function get($collection, $conditions = [], $options = []);
  /**
   * Query the database.
   *
   * @param string $collection table to query
   */
  public function query($collection) : BuilderInterface;
  /**
   * Store data in the database.
   *
   * @param string $name the name of the table
   * @param string/array $fields keypairs of columns/values to be stored
   * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
   *
   * @return array validation errors
   */
  public function store($name, $fields = [], $from = "auto");
  /**
   * Queue data to be stored in the database pending validation of other data.
   *
   * @param string $name the name of the table
   * @param string/array $fields keypairs of columns/values to be stored
   * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
   *
   * @return array validation errors
   */
  public function queue($name, $fields = [], $from = "auto", $unshift = false);
  /**
   * Proccess the queue of data for storage
   */
  public function storeQueue();
  /**
   * Remove from the database.
   *
   * @param string $from the name of the table
   * @param string $where the WHERE conditions on the DELETE
   */
  public function remove($from, $where);
  public function getPrefix();
  public function prefix($table);
  public function setDatabase($name);
  public function exec($statement);
  public function prepare($statement);
  public function lastInsertId();
  public function setInsertId($table, $id);
  public function getInsertId($table);
  public function errors($key = "", $values = false);
  public function error($error, $field = "global", $scope = "global");
  public function quoteIdentifier($str);
  public function getIdentifierQuoteCharacter();
}
