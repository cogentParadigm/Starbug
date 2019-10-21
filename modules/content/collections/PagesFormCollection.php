<?php
namespace Starbug\Content;

use Starbug\Core\FormCollection;

class PagesFormCollection extends FormCollection {
  public function build($query, $ops) {
    $query->select("path.alias as path");
    return parent::build($query, $ops);
  }
  public function filterRows($rows) {
    if ($this->copying) {
      foreach ($rows as $idx => $item) {
        if (!empty($item['id'])) {
          $blocks = $this->models->get("blocks")->query()->condition("id", $item['id'])->all();
          $item['blocks'] = [];
          foreach ($blocks as $block) {
            $item['blocks'][$block['region']."-".$block['position']] = $block['content'];
          }
        }
        unset($item['id']);
        unset($item['slug']);
        unset($item['path']);
        $rows[$idx] = $item;
      }
    }
    return $rows;
  }
}
