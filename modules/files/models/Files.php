<?php
class Files {

	function create($record) {
		$this->upload($record, $_FILES['file']);
	}
	
	function upload($record, $file) {
		if (!empty($file['name'])) {
			if ($file["error"] > 0) error($file["error"], "filename");
			$record['filename'] = str_replace(" ", "_", $file['name']);
			$record['mime_type'] = $this->get_mime($file['tmp_name']);
			$this->store($record);
			if ((!errors()) && (!empty($record['filename']))) {
				$id = (empty($record['id'])) ? $this->insert_id : $record['id'];
				return move_uploaded_file($file["tmp_name"], "app/public/uploads/".$id."_".$record['filename']);
			}
		} else {
			$record['filename'] = "";
			$this->store($record);
		}
	}
	
	function prepare() {
		$this->create(array("caption" => "Pre Uploaded File"));
		if (!errors()) $_POST['files']['id'] = $this->insert_id;
	}

	function delete($file) {
		$this->remove("id='" .$file['id'] ."'");
		return array();
	}
	
	function get_mime($file_path) {
		$mtype = '';
		if (function_exists('mime_content_type')){
			$mtype = mime_content_type($file_path);
		} else if (function_exists('finfo_file')){
			$finfo = finfo_open(FILEINFO_MIME);
			$mtype = finfo_file($finfo, $file_path);
			finfo_close($finfo);  
		}
		if ($mtype == '') {
			$mtype = "application/force-download";
		}
		return $mtype;
	}

}
?>
