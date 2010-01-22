<script type="text/javascript">
	var base_uri = '<?php echo uri(""); ?>';
</script>
<script type="text/javascript" src="<?php echo uri("core/app/public/js/nicedit/nicedit.js"); ?>"></script>
<script type="text/javascript">
//<![CDATA[
	bkLib.onDomLoaded(function() {
		var areas = dojo.query("textarea");
		areas.forEach(function(node, index, nodeList) {
			new nicEditor({fullPanel : true}).panelInstance(node.getAttribute("id"));
		});
	});
	dojo.require("dijit.form.TextBox");
	function dojifyElements() {
		dojo.query('.text').attr("dojoType", "dijit.form.TextBox");
		dojo.parser.parse();
	}
	dojo.addOnLoad(dojifyElements);
//]]>
</script>
