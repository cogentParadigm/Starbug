<?php
namespace Starbug\Db;

use Starbug\Core\DatabaseInterface;

/**
 * Run queries from the command line.
 */
class QueryCommand {
  /**
   * Command dependencies are just the DatabaseInterface to run queries.
   *
   * @param DatabaseInterface $db
   */
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function run($argv) {
    $name = array_shift($argv);
    $params = $this->parse($argv);
    $records = $this->db->query($name);
    foreach ($params as $key => $value) {
      call_user_func_array([$records, $key], [$value]);
    }
    echo $records->interpolate()."\n";
    if (!empty($params['limit']) && $params['limit'] == 1) $records = [$records];
    else $records = $records->execute();
    if (empty($records)) {
      echo "..no results\n";
    } else {
      $result = [];
      foreach ($records as $record) $result[] = array_values($record);
      $table = new \cli\Table();
      $table->setHeaders(array_keys($records[0]));
      $table->setRows($result);
      $table->display();
    }
  }
  public function parse($args) {
    $params = [];
    foreach ($args as $arg) {
      $arg = explode(":", $arg, 2);
      $params[$arg[0]] = $arg[1];
    }
    return $params;
  }
}
