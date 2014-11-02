<?php
class Files {

	function create($record) {
		$this->upload($record, $_FILES['file']);
	}

	function update($record) {
		$original = $this->get($record['id']);
		$this->store($record);
		if (!errors()) {
			if ($record['filename'] != $original['filename']) {
				//rename file
				$result = rename(BASE_DIR."/app/public/uploads/".$record['id']."_".$original['filename'], BASE_DIR."/app/public/uploads/".$record['id']."_".$record['filename']);
				if (!$result) $this->store(array("id" => $record['id'], "filename" => $original['filename']));
			}
		}
	}

	function upload($record, $file) {
		if (!empty($file['name'])) {
			if ($file["error"] > 0) error($file["error"], "filename");
			$record['filename'] = str_replace(" ", "_", $file['name']);
			$record['mime_type'] = $this->get_mime($file['tmp_name']);
			$record['size'] = filesize($file['tmp_name']);
			efault($record['category'], "files_category uncategorized");
			$this->store($record);
			if ((!errors()) && (!empty($record['filename']))) {
				$id = (empty($record['id'])) ? $this->insert_id : $record['id'];
				if (move_uploaded_file($file["tmp_name"], "app/public/uploads/".$id."_".$record['filename'])) {
					if (reset(explode("/", $record['mime_type'])) == "image") image_thumb("app/public/uploads/".$id."_".$record['filename'], "w:100  h:100  a:1");
					return true;
				} else {
					return false;
				}
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
		$this->remove("id:" .$file['id']);
		return array();
	}

	function get_mime($file_path) {
    	$output = exec("file --mime-type -b {$file_path}");
    	return $output;
		/*
		BUG: below code doesn't always work. sometimes finfo is not able to locate the file
		$mtype = '';
		if (function_exists('finfo_file')){
			$finfo = finfo_open(FILEINFO_MIME);
			$mtype = finfo_file($finfo, $file_path);
			finfo_close($finfo);
		}
		if ($mtype == '') {
			$mtype = "application/force-download";
		}
		return $mtype;
		*/
	}

	function query_list($query, &$ops) {
		$query->condition("files.statuses.slug", "deleted", "!=");
		if (!empty($ops['category']) && is_numeric($ops['category'])) {
			$query->condition("category", $ops['category']);
		}
		return $query;
	}

	function filter($file) {
		if (reset(explode("/", $file['mime_type'])) == "image") image_thumb("app/public/uploads/".$file['id']."_".$file['filename'], "w:100  h:100  a:1");
		return $file;
	}


}
?>
