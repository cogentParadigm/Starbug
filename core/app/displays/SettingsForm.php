<?php
namespace Starbug\Core;

class SettingsForm extends FormDisplay {
  public $model = "settings";
  public $cancel_url = "admin";
  public $defaultAction = "update";
  public function setDatabase(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function buildDisplay($options) {
    $settings = $this->db->query("settings")
      ->select("settings.*,category.term,category.slug")
      ->sort("settings_category.term_path, settings_category.position")
      ->all();
    $this->setPost([]);
    $last = "";
    foreach ($settings as $idx => $setting) {
      $this->setPost($setting['name'], $setting['value']);
      if ($setting['term'] != $last) {
        $last = $setting['term'];
        $this->add([$setting["slug"], "input_type" => "html", "value" => "<h1 class=\"well\">".$setting["term"]."</h1>"]);
      }
      $field = [$setting['name'], "input_type" => $setting['type']];
      if (!empty($setting['label'])) $field['label'] = $setting['label'];
      if (!empty($setting['options'])) $field += json_decode($setting['options'], true);
      if ($setting['type'] == "textarea") $field['data-dojo-type'] = 'dijit/form/Textarea';
      elseif ($setting['type'] == "checkbox") $field['value'] = 1;
      $this->add($field);
    }
  }
}
