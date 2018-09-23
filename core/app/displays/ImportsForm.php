<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;

class ImportsForm extends FormDisplay {
  public $source_keys = [];
  public $source_values = [];
  public $model = "imports";
  public $cancel_url = "admin/imports";
  public function setFilesystems(MountManager $filesystems) {
    $this->filesystems = $filesystems;
  }
  public function buildDisplay($options) {
    if ($options['operation'] == "run") {
      $this->buildRun($options);
    } else {
      $this->buildDefault($options);
    }
  }
  protected function buildDefault($options) {
    $source = $this->get("source");
    $model = $this->get("model");
    $this->add("name");
    $this->add(["model", "input_type" => "hidden", "default" => $options['model']]);
    $this->add(["action", "default" => "create"]);
    $this->add(["source", "input_type" => "file_select"]);
    if (!empty($source) && !empty($model)) {
      $this->add(["fields",
        "input_type" => "crud",
        "data-dojo-props" => [
          "get_data" => "{model:'".$model."',  source:'".$source."'}"
        ]
      ]);
    }
  }
  protected function buildRun($options) {
    $this->actions->remove($this->defaultAction);
    $source = $this->get("source");
    $output = $this->preparePaginatedOutput($source);
    if ($this->success("run")) {
      $this->add(["success", "input_type" => "html", "value" => '<p class="alert alert-success">Import completed</p>']);
    }
    $this->add(["table", "input_type" => "template", "value" => "csv-table.html", "class" => "table table-striped"] + $output);
    $this->add(["count", "input_type" => "html", "value" => "<p>".$output["pager"]->count." rows. Press import to begin.</p>"]);
    $this->actions->add(["run", "label" => "Import", "class" => "btn-success"]);
  }
  protected function preparePaginatedOutput($id) {
    $rows = [];
    $file = $this->models->get("files")->query()->condition("id", $id)->one();
    $count = 0;
    if (false !== ($handle = $this->filesystems->readStream($file["location"]."://".$file["id"]."_".$file["filename"]))) {
      while (!feof($handle)) {
        if (fgets($handle)) $count++;
      }
    }
    $count--;
    $size = 20;
    $pager = new Pager($count, $size, intval($this->request->getParameter('pg')));
    $line = 0;
    rewind($handle);
    $rows[] = fgetcsv($handle);
    while ($row = fgetcsv($handle)) {
      $line++;
      if ($line <= $pager->start) continue;
      if ($line > $pager->finish) break;
      $rows[] = $row;
    }
    fclose($handle);
    $vars = $this->request->getParameters();
    unset($vars['pg']);
    $prefix = $this->request->getURL()->getDirectory();
    $prefix .= $this->request->getPath()."?";
    if (!empty($vars)) $prefix .= http_build_query($vars).'&';
    $prefix .= "pg=";
    $half = floor($pager->range/2);
    // set $from to $current_page minus half of $range OR 1
    $fromPage = ($pager->current_page > $half) ? $pager->current_page - $half : 1;
    // set $to to the full range from from
    $toPage = $fromPage + $pager->range;
    // if that pushes us past the end, shift back to the end
    if ($toPage > $pager->last) {
      $toPage = $pager->last;
      $fromPage = $toPage - $pager->range;
    }
    // if there are not enough pages, bring up $from to 1
    if ($fromPage < 1) $fromPage = 1;
    return ["rows" => $rows, "pager" => $pager, "url" => $prefix, "fromPage" => $fromPage, "toPage" => $toPage];
  }
}
