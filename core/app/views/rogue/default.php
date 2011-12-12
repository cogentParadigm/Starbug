<?php
	$browse = $_REQUEST['browse'];
	if(!$browse) $browse = ".";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>The Bridge</title>
		<link rel="stylesheet" type="text/css" href="<?php echo uri("app/public/js/dojo/release/dojo/dijit/themes/claro/claro.css"); ?>"/>
		<?php $sb->publish("head"); ?>
		<script src="<?php echo uri("app/public/js/CodeMirror/lib/codemirror.js"); ?>" type="text/javascript"></script>
		<script src="<?php echo uri("app/public/js/CodeMirror/mode/clike/clike.js"); ?>" type="text/javascript"></script>
		<script src="<?php echo uri("app/public/js/CodeMirror/mode/php/php.js"); ?>" type="text/javascript"></script>
		<script src="<?php echo uri("app/public/js/CodeMirror/mode/javascript/javascript.js"); ?>" type="text/javascript"></script>
		<script src="<?php echo uri("app/public/js/CodeMirror/mode/htmlmixed/htmlmixed.js"); ?>" type="text/javascript"></script>
		<script src="<?php echo uri("app/public/js/CodeMirror/mode/css/css.js"); ?>" type="text/javascript"></script>
		<script src="<?php echo uri("app/public/js/CodeMirror/mode/xml/xml.js"); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo uri("app/public/js/dojo/release/dojo/starbug/ide.js"); ?>"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo uri("app/public/js/CodeMirror/lib/codemirror.css"); ?>"/>
		<link rel="stylesheet" type="text/css" href="<?php echo uri("app/public/js/CodeMirror/theme/default.css"); ?>"/>
		<style type="text/css">
			html,body{height:100%}
			body{margin:0px;font-size:14px;color:#555;background:#fff}
			a, a:hover{color:#00F}
			#explorer{margin-bottom:5px}
			tbody tr td{background:#fff}
			tbody tr:nth-child(even) td, tbody tr.even td{background:#F5F9FF}
			tbody tr:hover td, tbody tr.highlight td{background: #efe}
			.fileinfo{margin:0;text-align:right}
			.alerts .error{width:auto}
			.CodeMirror{font-size:12px}
			.tundra .dijitTabCloseButton{cursor:pointer}
			body.claro .dijitTabContainerTop-tabs .dijitTab{padding-top:0px}
			body.claro .dijitAccordionContainer .dijitAccordionContainer-child{padding:0;margin:0}
			.CodeMirror-scroll{height:auto;overflow:visible}
			.console .error, .console .alert, .console .notice, .console .success, .console .info{background:transparent;border:none;padding:0;margin-bottom:8px}
		</style>
		<script type="text/javascript">
			require(["starbug/IDE/IDE"]);
		</script>
	</head>
	<body id="body" class="claro">
		<div jsId="ide" data-dojo-type="starbug.IDE.IDE" style="height:100%"
			browseURL="<?php echo uri("rogue/browse"); ?>"
			openURL="<?php echo uri("rogue/open"); ?>"
			saveURL="<?php echo uri("rogue/save"); ?>"
			errorURL="<?php echo uri("rogue/errors"); ?>"
			rogueURL="<?php echo uri("rogue"); ?>"
		<?php if (!empty($_REQUEST['files'])) { ?>
			files="['<?php echo str_replace(",", "', '", $_REQUEST['files']); ?>']"
		<?php } ?>
			startDir="<?php echo $browse; ?>"
			gitURL="<?php echo uri("rogue/git"); ?>"
		/>
	</body>
</html>
