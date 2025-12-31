<?php
namespace Starbug\Settings\Admin;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Templates\TemplateInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;

class SettingsForm extends FormDisplay {
  public $model = "settings";
  public $cancel_url = "admin";
  public $defaultAction = "update";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    protected DatabaseInterface $db
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
  }
  public function buildDisplay($options) {
    $settings = $this->db->query("settings")
      ->select("settings.*")
      ->select("category.name as term")
      ->select("category.slug")
      ->sort("settings_category.position")
      ->all();
    $this->setPost([]);
    $last = "";
    foreach ($settings as $idx => $setting) {
      $this->setPost($setting['name'], $setting['value']);
      if ($setting["term"] != $last) {
        $marginTop = $last ? "mt4" : "mt0";
        $last = $setting["term"];
        $this->add([$setting["slug"], "input_type" => "html", "value" => "<h2 class=\"f7 mb2 {$marginTop} ttu\">".$setting["term"]."</h1>"]);
      }
      $field = [$setting['name'], "input_type" => $setting['type'], "autocomplete" => "off"];
      if (!empty($setting['label'])) {
        $field['label'] = $setting['label'];
      }
      if (!empty($setting['options'])) {
        $field += json_decode($setting['options'], true);
      }
      if ($setting['type'] == "textarea") {
        $field['data-dojo-type'] = 'dijit/form/Textarea';
      } elseif ($setting['type'] == "checkbox") {
        $field['value'] = 1;
      }
      $this->add($field);
    }
  }
}
