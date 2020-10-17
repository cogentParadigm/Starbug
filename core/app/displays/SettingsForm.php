<?php
namespace Starbug\Core;

class SettingsForm extends FormDisplay {
  public $model = "settings";
  public $cancel_url = "admin";
  public $defaultAction = "update";
  public function buildDisplay($options) {
    $settings = $this->db->query("settings")
      ->select("settings.*,category.term,category.slug")
      ->sort("settings_category.term_path, settings_category.position")
      ->all();
    $this->request->setPost('settings', []);
    $last = "";
    foreach ($settings as $idx => $setting) {
      $this->request->setPost('settings', $setting['name'], $setting['value']);
      if ($setting['term'] != $last) {
        $last = $setting['term'];
        $this->layout->add([$setting['slug']."-row", $setting['slug'] => "div.col-xs-12"]);
        $this->layout->put($setting['slug'], 'h1#'.$setting['slug'].'.well', $setting['term']);
      }
      $field = [$setting['name'], "input_type" => $setting['type'], "pane" => $setting['slug']];
      if (!empty($setting['label'])) $field['label'] = $setting['label'];
      if (!empty($setting['options'])) $field += json_decode($setting['options'], true);
      if ($setting['type'] == "textarea") $field['data-dojo-type'] = 'dijit/form/Textarea';
      elseif ($setting['type'] == "checkbox") $field['value'] = 1;
      $this->add($field);
    }
  }
}
