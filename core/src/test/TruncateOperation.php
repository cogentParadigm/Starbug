<?php
namespace Starbug\Core;

use PHPUnit\DbUnit\Operation\Truncate;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;

/**
 * Disables foreign key checks temporarily.
 */
class TruncateOperation extends Truncate {
  public function execute(Connection $connection, IDataSet $dataSet) {
    $connection->getConnection()->query("SET foreign_key_checks = 0");
    parent::execute($connection, $dataSet);
    $connection->getConnection()->query("SET foreign_key_checks = 1");
  }
}
