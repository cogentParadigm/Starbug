<?php
namespace Starbug\Imports\Read\PhpSpreadsheet\IReadFilter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class HeaderRowFilter implements IReadFilter {
  public function readCell($column, $row, $worksheetName = '') {
    if ($row == 1) {
      return true;
    }
    return false;
  }
}
