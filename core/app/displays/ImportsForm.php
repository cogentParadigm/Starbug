<?php
namespace Starbug\Core;
use League\Flysystem\Filesystem;
class ImportsForm extends FormDisplay {
	public $source_keys = array();
	public $source_values = array();
	public $model = "imports";
	public $cancel_url = "admin/imports";
	public function setFilesystems(MountManager $filesystems) {
		$this->filesystems = $filesystems;
	}
	function build_display($options) {
		if ($options['operation'] == "run") {
			$this->build_run($options);
		} else {
			$this->build_default($options);
		}
	}
	function build_default($options) {
		$source = $this->get("source");
		$model = $this->get("model");
		$this->add("name");
		$this->add(["model", "input_type" => "hidden", "default" => $options['model']]);
		$this->add(["action", "default" => "create"]);
		$this->add(["source", "input_type" => "file_select"]);
		if (!empty($source) && !empty($model)) {
			$this->add(array("fields",
				"input_type" => "crud",
				"data-dojo-props" => array(
					"get_data" => "{model:'".$model."',  source:'".$source."'}"
				)
			));
		}
	}
	function build_run($options) {
		$this->actions->remove($this->default_action);
		$source = $this->get("source");
		$lines = $this->count_source($source);
		if ($this->success("run")) {
			$this->add(array("success", "input_type" => "html", "value" => '<p class="alert alert-success">Import completed</p>'));
		}
		$this->add(array("table", "input_type" => "template", "value" => "csv-table", "class" => "table table-striped", "csv" => $source));
		$this->add(array("count", "input_type" => "html", "value" => "<p>".$lines." rows. Press import to begin.</p>"));
		$this->actions->add(["run", "label" => "Import", "class" => "btn-success"]);
	}
	function count_source($id) {
		$file = $this->models->get("files")->query()->condition("id", $id)->one();
		$$lines = 0;
		if (false !== ($handle = $this->filesystems->readStream($file["location"]."://".$file["id"]."_".$file["filename"])["stream"])) {
			while (!feof($handle)) {
				if (fgets($handle)) $lines++;
			}
		}
		fclose($handle);
		return $lines-1;
	}
}
