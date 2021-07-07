<?php
namespace Starbug\Content;

use Starbug\Core\FormCollection;

class PagesFormCollection extends FormCollection {
  public function build($query, $ops) {
    $query->select("path.alias as path");
    return parent::build($query, $ops);
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $item) {
      if (!empty($item['id'])) {
        $blocks = $this->db->query("blocks")->condition("pages_id", $item['id'])->all();
        $item['blocks'] = [];
        foreach ($blocks as $block) {
          $item['blocks'][$block['region']."-".$block['position']] = $block['content'];
        }
      }
      if ($this->copying) {
        unset($item['id']);
        unset($item['slug']);
        unset($item['path']);
      }
      $rows[$idx] = $item;
    }
    return $rows;
  }
}
