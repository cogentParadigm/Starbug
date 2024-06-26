<?php
namespace Starbug\Db;

use Starbug\Db\Query\BuilderFactory;
use Starbug\Bundle\Bundle;
use Starbug\Db\Query\BuilderInterface;
use Starbug\Log\LoggerFactory;

abstract class AbstractDatabase implements DatabaseInterface {
  public $errors;
  /**
   * Holds the number of records returned by last query.
   *
   * @var int
   */
  public $record_count;
  /**
   * Table prefix.
   *
   * @var string
   */
  protected $prefix;
  /**
   * Name of database.
   *
   * @var string
   */
  protected $database_name;
  /**
   * Holds records waiting to be stored
   *
   * @var QueryQueue
   */
  protected $queue;

  protected $operators = [
    '=' => 1,
    '>' => 1,
    '<' => 1,
    '<=' => 1,
    '>=' => 1,
    '<>' => 1,
    '<=>' => 1,
    '!=' => 1,
    'LIKE' => 1,
    'RLIKE' => 1,
    'NOT LIKE' => 1,
    'NOT RLIKE' => 1
  ];
  protected $identifierQuoteChar = "\"";
  protected $config;
  protected $params;
  protected $models;
  protected $insertIds = [];
  protected $timezone = false;
  protected $logger;

  const PHASE_VALIDATION = 0;
  const PHASE_STORE = 1;
  const PHASE_AFTER_STORE = 2;
  const PHASE_BEFORE_DELETE = 3;
  const PHASE_AFTER_DELETE = 4;

  public function __construct(
    protected BuilderFactory $queryBuilderFactory,
    LoggerFactory $loggerFactory
  ) {
    $this->queryBuilderFactory = $queryBuilderFactory;
    $this->queue = new QueryQueue();
    $this->errors = new Bundle();
    $this->logger = $loggerFactory->create("db");
  }

  public function getPrefix() {
    return $this->prefix;
  }

  public function setTimeZone($timezone) {
    $this->timezone = $timezone;
  }

  /**
   * Get records or columns.
   *
   * @param string $model the name of the model
   * @param mixed $id/$conditions the id or an array of conditions
   * @param string $column optional column name
   */
  public function get($collection, $conditions = [], $options = []) {
    $args = func_get_args();
    $query = $conditions = $arg = [];

    // loop through the input arguments
    foreach ($args as $idx => $a) {
      if ($idx == 0) {
        $collection = $a; // first argument is the collection
      } elseif ($idx == 1) {
        $conditions = $a; // second argument are the conditions
      } else {
        $arg = $a;
        if (!empty($arg['orderby'])) {
          $arg['sort'] = $arg['orderby']; // DEPRECATED: use sort
        }
      }
    }
    $args = $arg;

    // apply conditions
    $query = $this->query($collection);
    if (!is_array($conditions)) {
      $conditions = [$conditions];
    }
    foreach ($conditions as $k => $v) {
      if (isset($this->operators[$k])) {
        $query->conditions($v, $k);
      } else {
        $col = ($k === 0) ? "id" : $k;
        // if id is compared for equality, set the limit to 1
        if ($col === "id" && !is_array($v)) {
          $args['limit'] = 1;
        }
        $query->condition($col, $v);
      }
    }

    if (!empty($args['sort'])) {
      foreach ($args['sort'] as $key => $direction) {
        $query->sort($key, $direction);
      }
    }
    if (!empty($args['limit'])) {
      $query->limit($args['limit']);
    }
    if (!empty($args['skip'])) {
      $query->skip($args['skip']);
    }


    // obtain query result
    $result = $query->execute();
    return $result;
  }

  /**
   * Query the database.
   *
   * @param string $froms comma delimeted list of tables to join. 'users' or 'users,permits'
   * @param string $args starbug query string for params: select, where, limit, and action/priv_type
   * @param bool $mine optional. if true, joining models will be checked for relationships and ON statements will be added
   *
   * @return array record or records
   */
  public function query($froms, $args = [], $replacements = []) : BuilderInterface {
    return $this->queryBuilderFactory->create($this)->from($froms);
  }

  /**
   * Store data in the database
   *
   * @param string $name the name of the table
   * @param string/array $fields keypairs of columns/values to be stored
   * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
   */
  public function store($name, $fields = [], $from = "auto") {
    $this->queue($name, $fields, $from, true);
    // $last = array_pop($this->to_store);
    // $this->to_store = array_merge(array($last), $this->to_store);
    $this->storeQueue();
  }

  /**
   * Queue data to be stored in the database pending validation of other data
   *
   * @param string $name the name of the table
   * @param string/array $fields keypairs of columns/values to be stored
   * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
   */
  public function queue($name, $fields = [], $from = "auto", $unshift = false) {
    $query = $this->queryBuilderFactory->create($this)->from($name)->set($fields);

    if ($from === "auto" && !empty($fields['id'])) {
      $from = ["id" => $fields['id']];
    }

    if (!empty($from) && is_array($from)) {
      $query->mode("update");
      foreach ($from as $c => $v) {
        $query->condition($c, $v);
      }
    } else {
      $query->mode("insert");
    }

    if ($unshift) {
      $this->queue->unshift($query);
    } else {
      $this->queue->push($query);
    }
  }

  /**
   * Proccess the queue of data for storage
   */
  public function storeQueue() {
    $this->queue->execute();
  }

  /**
   * Remove from the database
   *
   * @param string $from the name of the table
   * @param string $where the WHERE conditions on the DELETE
   */
  public function remove($from, $where) {
    if (!empty($where)) {
      $del = $this->queryBuilderFactory->create($this)->from($from);
      $this->record_count = $del->condition($where)->delete();
      return $this->record_count;
    }
  }

  public function prefix($table) {
    if (substr($table, 0, 1) == "(") {
      return $table;
    }
    return $this->prefix.$table;
  }

  public function errors($key = "", $values = false) {
    if (is_bool($key)) {
      $values = $key;
      $key = "";
    }
    $parts = empty($key) ? [] : explode(".", $key);
    if ($values) {
      return $this->errors->get(...$parts);
    } elseif (!empty($parts)) {
      return $this->errors->has(...$parts);
    } else {
      return !$this->errors->isEmpty();
    }
  }

  public function error($error, $field = "global", $scope = "global") {
    $parts = array_merge([$scope], explode(".", $field));
    $parts[] = is_array($error) ? $error : [$error];
    $this->errors->set(...$parts);
    $this->logger->info("{$scope}.{$field}: {$error}");
  }

  public function setInsertId($table, $id) {
    $this->insertIds[$table] = $id;
  }

  public function getInsertId($table) {
    return isset($this->insertIds[$table]) ? $this->insertIds[$table] : null;
  }

  public function quoteIdentifier($str) {
    $char = $this->getIdentifierQuoteCharacter();
    return $char . $str . $char;
  }

  public function getIdentifierQuoteCharacter() {
    return $this->identifierQuoteChar;
  }
}
