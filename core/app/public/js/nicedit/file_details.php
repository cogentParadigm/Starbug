<?php
include("config.inc.php");
$file = basename($_GET['file']);
?>
<div id="optionsWrapper2">
	<p>
    	<b>URL:</b> <? echo _path.$file; ?><br />
		<a href="javascript:deletefile();">Delete This File</a>
        &nbsp;&nbsp;-&nbsp;&nbsp;
        <a href="javascript:dialog_close('<?php echo _path.$file; ?>');">Insert this image</a>
    </p>
</div>
    <?php
	$extension=substr(strtolower($_GET['file']), -4, 4);
	if ($extension==".jpg" || $extension==".gif" || $extension==".png" || $extension==".jpeg" ) {
		echo '
		<div id="imgWrapper">
			<img src="'.$_GET['file'].'">
		</div>
		';
	} else {
		echo '<a href="'.$_GET['file'].'" target="_blank">See this file</a>';
	}
?>	
<script language="javascript">
function deletefile(codigo) {
	var fRet;
	fRet = confirm('Do you want delete this file?');
	if (fRet==false) {
		return;
	} else { 
		window.location='index.php?action=delete&file=<?php echo $_GET['file']; ?>';
	}
};
function dialog_close(cual) {
	var o = opener.document.getElementById("src");
	o.value = cual;
	self.close();	
}
</script>
