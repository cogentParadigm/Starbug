<?php
namespace Starbug\Content;

use Starbug\Core\FormHook;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\RequestInterface;

class FormBlocksHook extends FormHook {
  public function __construct(DatabaseInterface $db, RequestInterface $request) {
    $this->db = $db;
    $this->request = $request;
  }
  public function build($form, &$control, &$field) {
    $containers = [["region" => "content", "position" => 1, "content" => "", "type" => "text"]];
    $data = $this->request->getPost();
    $item_id = $form->get("id");
    if (!empty($item_id)) {
      $containers = $this->db->query("blocks")->condition("pages_id", $form->get("id"))->sort("position")->all();
    } elseif (!empty($data[$form->model]['blocks'])) {
      $containers = [];
      foreach ($data[$form->model]['blocks'] as $key => $content) {
        list($region, $position) = explode("-", $key);
        $containers[] = ["region" => $region, "position" => $position, "content" => $content, "type" => "text"];
      }
    }
    $field['nolabel'] = true;
    $field['class'] = "rich-text";
    $field['style'] = "width:100%;height:100px";
    $field['containers'] = $containers;
  }
}
