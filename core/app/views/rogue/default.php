<?php
	$browse = $_REQUEST['browse'];
	if(!$browse) $browse = ".";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>The Bridge</title>
		<link rel="stylesheet" type="text/css" href="<?php echo uri("app/public/js/dojo/release/dojo/dijit/themes/claro/claro.css"); ?>"/>
		<?php $sb->publish("header"); ?>
		<script type="text/javascript" src="<?php echo uri("app/public/js/dojo/release/dojo/starbug/ide.js"); ?>"></script>
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
			.CodeMirror-line-numbers{padding:5px;background:#eee}
			.CodeMirror-line-numbers div{font-size:10pt;line-height:1.2em;text-align:right}
			.tundra .dijitTabCloseButton{cursor:pointer}
			body.claro .dijitTabContainerTop-tabs .dijitTab{padding-top:0px}
			body.claro .dijitAccordionContainer .dijitAccordionContainer-child{padding:0;margin:0}
		</style>
		<script src="<?php echo uri("app/public/js/CodeMirror/js/codemirror.js"); ?>" type="text/javascript"></script>
		<script type="text/javascript">
			dojo.require("starbug.IDE.IDE");
		</script>
	</head>
	<body id="body" class="claro">
		<div jsId="ide" dojoType="starbug.IDE.IDE" style="height:100%"
			browseURL="<?php echo uri("rogue/browse"); ?>"
			openURL="<?php echo uri("rogue/open"); ?>"
			saveURL="<?php echo uri("rogue/save"); ?>"
			errorURL="<?php echo uri("rogue/errors"); ?>"
		<?php if (!empty($_REQUEST['files'])) { ?>
			files="['<?php echo str_replace(",", "', '", $_REQUEST['files']); ?>']"
		<?php } ?>
			startDir="<?php echo $browse; ?>"
			gitURL="<?php echo uri("rogue/git"); ?>"
		/>
	</body>
</html>