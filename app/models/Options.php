<?php
/**
 * options model
 * @ingroup models
 */
class Options extends OptionsModel {

	function create($options) {
		efault($options['seo_hide'], 0);
		foreach ($options as $k => $v) $this->store(array("value" => $v), "name:$k");
	}

	function delete($option) {
		return $this->remove('id='.$option['id']);
	}

}
?>
