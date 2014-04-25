<?php
/**
 * options model
 * @ingroup models
 */
class Settings {

	function update($settings) {
		efault($settings['seo_hide'], 0);
		foreach ($settings as $k => $v) $this->store(array("value" => $v), "name:$k");
	}

	function delete($setting) {
		return $this->remove('id:'.$setting['id']);
	}

}
?>
