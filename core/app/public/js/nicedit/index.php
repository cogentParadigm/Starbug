<?php
include("config.inc.php");

// upload
if ($_REQUEST['action']=="upload") {
	$file_name=$_FILES["ffoto"]["name"];
	$file_size=$_FILES["ffoto"]["size"];
	$file_type=$_FILES["ffoto"]["type"];
	$path=$_POST['folder'];
	move_uploaded_file($_FILES['ffoto']['tmp_name'],$path.$file_name);
	$message='<p><b>Your file has been uploaded successfully</b></p>';
} else if ($_REQUEST['action']=="delete") {
	unlink($_GET['file']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Image</title>
<script language="javascript" src="jquery-1.2.6.min.js"></script>
<script language="javascript" src="jqueryFileTree.js"></script>
<script language="javascript" src="jquery.form.js"></script>
<script language="javascript" src="jquery.jframe.js"></script>
<link href="jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />
<link href="file_manager.css" rel="stylesheet" type="text/css" />
<script language="javascript">
function loadMyFrame(url,target){
	jQuery(target).loadJFrame(url);
	$(target).loadJFrame(url);
}
</script>
<script language="javascript">
$(document).ready(function(){
   	$('#navBar').fileTree({ 
		//root: '../../userfiles/', 
		root: '<?php echo _folder; ?>', 
		script: 'jqueryFileTree.php', 
		loadMessage: 'Loading...',
		exts: 'jpeg,jpg,png,gif,tiff,pdf' }
		, function(file) { 
			//alert(file);
			loadMyFrame('file_details.php?file='+file, '#fileDetails');
	});
	jQuery.fn.waitingJFrame = function () {
    	$(this).html("<b>loading...</b>");
	};		
});
</script>
</head>
<body>
<div id="fileWrapper" src="#">		
	
	<div id="optionsWrapper">
	<strong>File Manager</strong>			
	<form id="uploadForm" method="post" action="" enctype="multipart/form-data">
		<input type="file" name="ffoto">
		<input name="action" type="hidden" value="upload" />
        <input type="hidden" name="folder" value="<?php echo _folder; ?>" />
        <input type="submit" value="Upload" class="btn" target="_self" />
     </form>
    <!-- <div id="uploadOutput"></div>-->
	</div>	
	
	<div id="navBar" class="demo" src="#"></div>
    <div id="fileDetails">
    	<?php echo $message; ?>
    	Please select file to edit
    </div>
</div>	


</body>
</html>
