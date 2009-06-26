<?php
//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//
include("../../../../etc/Etc.php");
include("../../../../util/thumb.php");
$_POST['dir'] = urldecode($_POST['dir']);
if( file_exists($root . $_POST['dir']) ) {
	$files = scandir($root . $_POST['dir']);
	natcasesort($files);
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";		
		// All dirs
		?>
        <!--
		./<span class="op_menu" onclick="javascript:upload_file('<?php echo htmlentities($_POST['dir'] . $file); ?>');">options</span>
        -->
        <?php
		foreach( $files as $file ) {
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
				echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "
				</a>";
				?>
                <span class="op_menu" onclick="javascript:upload_file('<?php echo htmlentities($_POST['dir'] . $file); ?>/');">options</span>
                <?php
				echo "</li>";
			} 			
		}		
		// All files
		?>
        <!--
		<a class="opfolder" href="javascript:upload_file('<?php echo $_POST['dir']; ?>');">Options</a>        
        -->
        <?php
		foreach( $files as $file ) {
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
				$ext = preg_replace('/^.*\./', '', $file);
				echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . "<img src=\"".get_thumb("public/uploads/$file", 60)."\" />" . "</a></li>";
			}
		}		
		echo "</ul>";	
	}
}
?>
