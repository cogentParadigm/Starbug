<?php
namespace Starbug\Log\Collection;

use Starbug\Admin\Db\Query\AdminCollection;

class AdminErrorLogCollection extends AdminCollection {
  protected $model = "error_log";
  protected $logLevels = [
    100 => "Debug",
    200 => "Info",
    250 => "Notice",
    300 => "Warning",
    400 => "Error",
    500 => "Critical",
    550 => "Alert",
    600 => "Emergency"
  ];
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["level"])) {
      $query->condition("level", $ops["level"]);
    }
    if (!empty($ops["sort"])) {
      $query->sort("time DESC");
    }
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row["level"] = $this->logLevels[$row["level"]];
      $row["message"] = nl2br($row["message"]);
    }
    return $rows;
  }
}
