<?php
/**
 * options model
 * @ingroup models
 */
namespace Starbug\Core;
class Settings extends SettingsModel {

	function update($settings) {
		foreach ($settings as $k => $v) $this->store(array("value" => $v), "name:$k");
	}

	function delete($setting) {
		$this->remove($setting['id']);
	}

}
?>
