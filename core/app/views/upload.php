<?php
	//file_put_contents(BASE_DIR."/files.log", json_encode($_FILES));
	$_post = array();
	$htmldata = array();
	$record = array("filename" => "", "mime_type" => "", "caption" => "uploaded file", "category" => $_POST['category']);
	$file = array();
	foreach ($_FILES['uploadedfiles'] as $key => $arr) $file[$key] = $arr[0];
	if (!empty($file['category'])) $record['category'] = $file['category'];
	$moved = sb("files")->upload($record, $file);
	if ($moved) {
		$id = sb('insert_id');
		$_post['id'] = $id;
		$_post['original_name'] = str_replace(" ", "_", $file['name']); 
		$_post['name'] = $id."_".$_post['original_name'];
		$_post['file'] = "app/public/uploads/".$_post['name'];
		$_post['mime_type'] = sb("files")->get_mime($_post['file']);
		try{
			list($width, $height) = getimagesize($_post['file']);
			$image = true;
		} catch(Exception $e){
			$width=0;
			$height=0;
			$image = false;
		}
		$_post['width'] = $width;
		$_post['height'] = $height;
		$_post['type'] = end(explode(".", $_post['name']));
		$_post['size'] = filesize($_post['file']);
		$_post['image'] = $image;
		$_post['status'] = 4;
		$_post['owner'] = userinfo("id");
		$htmldata[] = $_post;
	} else {
		$htmldata[] = array("ERROR" => "File could not be moved: ".$file['name']);
	}
	echo json_encode($htmldata);
?>
