<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ModelTest.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup ModelTest
 */
/**
 * @defgroup ModelTest
 * the base test class for models
 * @ingroup test
 */
namespace Starbug\Core;
/**
 * Disables foreign key checks temporarily.
 */
class TruncateOperation extends \PHPUnit_Extensions_Database_Operation_Truncate {
	public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet) {
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}
?>
