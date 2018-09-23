<?php
namespace Starbug\Core;

class Settings extends SettingsModel {

  public function update($settings) {
    foreach ($settings as $k => $v) $this->store(["value" => $v], ["name" => $k]);
  }

  public function delete($setting) {
    $this->remove($setting['id']);
  }
}
