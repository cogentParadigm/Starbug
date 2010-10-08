<?php
class Files extends FilesModel {

	function create() {
		$record = $_POST['files'];
		$errors = $this->upload($record, $_FILES['file']);
		return $errors;
	}
	
	function upload($record, $file) {
		if (!empty($file['name'])) {
			if (!($file["size"] < 1000000)) return array("filename" => array("size" => "You must upload a file that is less than ".(round(1000000/1024))."kB"));
			if ($file["error"] > 0) return array("filename" => array("file" => $file["error"]));
			$record['filename'] = str_replace(" ", "_", $file['name']);
			$record['mime_type'] = $this->get_mime($file['tmp_name']);
			$errors = $this->store($record);
			if ((empty($errors)) && (!empty($record['filename']))) {
				$id = (empty($record['id'])) ? $this->insert_id : $record['id'];
				move_uploaded_file($file["tmp_name"], "app/public/uploads/".$id."_".$record['filename']);
			}
		} else {
			$record['filename'] = "";
			$errors = $this->store($record);
		}
		return $errors;
	}

	function delete() {
		$this->remove("id='" .$_POST['files']['id'] ."'");
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
