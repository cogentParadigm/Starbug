<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/query.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
namespace Starbug\Core;
class DescribeCommand {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function run($argv) {
    $name = array_shift($argv);
    $records = $this->db->pdo->query("DESCRIBE `".$this->db->prefix($name)."`")->fetchAll(\PDO::FETCH_ASSOC);
    \cli::table($records);
  }
}
?>
