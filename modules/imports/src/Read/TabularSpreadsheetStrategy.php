<?php
namespace Starbug\Imports\Read;

use Iterator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Starbug\Imports\Import;

class TabularSpreadsheetStrategy extends FileStrategy {
  protected $worksheet;

  public function __construct($path, $worksheet = "") {
    $this->path = $path;
    $this->worksheet = $worksheet;
  }
  public function getRows(Import $import, $params = []) : Iterator {
    $spreadsheet = $this->getSpreadsheet($this->path, $this->worksheet);
    $worksheet = $spreadsheet->getActiveSheet();
    $head = $this->getHeadRow($worksheet);
    foreach ($worksheet->getRowIterator(2) as $row) {
      $this->currentRow = $row->getRowIndex();
      $source = $this->getRowArray($worksheet, $row, $head);
      $dest = $this->getMappedValues($source, $import->getFields());
      yield $dest;
    }
    unset($worksheet);
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
  }
  protected function getHeadRow(Worksheet $worksheet) {
    $head = [];
    foreach ($worksheet->getRowIterator(1, 1) as $row) {
      foreach ($row->getCellIterator() as $cell) {
        $head[$cell->getValue()] = $cell->getColumn();
      }
    }
    return $head;
  }
  protected function getRowArray(Worksheet $worksheet, Row $row, $head) {
    $array = [];
    foreach ($head as $label => $column) {
      $array[$label] = $worksheet
        ->getCell($column.$row->getRowIndex())
        ->getFormattedValue();
    }
    return $array;
  }
  protected function getSpreadsheet($path, $worksheet = "") {
    $type = IOFactory::identify($path);
    $reader = IOFactory::createReader($type);
    if (!empty($worksheet)) {
      $reader->setLoadSheetsOnly($worksheet);
    }
    $spreadsheet = $reader->load($path);
    $sheets = $spreadsheet->getSheetNames();
    if (!empty($worksheet) && !in_array($worksheet, $sheets)) {
      throw new WorksheetNotFoundException("The worksheet '".$worksheet."' could not be found.");
    }
    return $spreadsheet;
  }
}
