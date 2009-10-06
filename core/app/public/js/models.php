<script type="text/javascript">
function activate_model(loc, backup) {
	if ((backup) && !confirm("A backup model exists in the 'app/models/' directory. Press 'OK' to use this backup, or 'Cancel' to delete it and generate a new one.")) {
		dojo.byId("restore_backup").setAttribute('value', 0);
	}
	dojo.xhrPost({
		url: '<?php echo uri("sb/xhr/models/get/model/"); ?>'+loc,
		form: 'activate_'+loc,
		load: function(data) {
			var loc_node = dojo.byId(loc);
			loc_node.setAttribute('class', '');
			loc_node.innerHTML = data;
		}
	});
}
function activate_field(prefix, loc) {
	dojo.xhrPost({
		url: '<?php echo uri("sb/xhr/models/get/model/"); ?>'+prefix+'-'+loc,
		form: 'activate_'+prefix+'-'+loc,
		load: function(data) {
			var loc_node = dojo.byId(prefix);
			loc_node.innerHTML = data;
		}
	});
}
function deactivate_model(loc) {
	dojo.xhrPost({
		url: '<?php echo uri("sb/xhr/models/get/model/"); ?>'+loc,
		form: 'deactivate_'+loc,
		load: function(data) {
			var loc_node = dojo.byId(loc);
			loc_node.setAttribute('class', 'inactive');
			loc_node.innerHTML = data;
		}
	});
}
</script>
