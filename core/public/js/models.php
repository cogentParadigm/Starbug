<script type="text/javascript">
function new_key(loc) {
	dojo.xhrGet({
		url: '<?php echo uri("models/new/key"); ?>/'+loc,
		load: function (data) {
			dojo.byId(loc).innerHTML += data;
		}
	});
}
function save_new_key(loc) {
	dojo.xhrPost({
		url: '<?php echo uri("models/get/key"); ?>',
		form: 'new_key_form',
		load: function(data) {
			cancel_new_key();
			dojo.byId(loc+'-fields').innerHTML += data;
		}
	});
}
function cancel_new_key() {
	var newrow = dojo.byId('new_key');
	newrow.parentNode.removeChild(newrow);
}
function edit_key(loc) {
	dojo.xhrGet({
		url: '<?php echo uri("models/edit/key/"); ?>'+loc,
		load: function(data) {
			dojo.byId(loc).innerHTML = data;
		}
	});
}
function save_edit_key(loc) {
	dojo.xhrPost({
		url: '<?php echo uri("models/get/key/"); ?>'+loc,
		form: 'edit_key_form',
		load: function(data) {
			dojo.byId(loc).innerHTML = data;
		}
	});
}
function cancel_edit_key(loc) {
	dojo.xhrGet({
		url: '<?php echo uri("models/get/key/"); ?>'+loc,
		load: function(data) {
			dojo.byId(loc).innerHTML = data;
		}
	});
}
function delete_key(loc) {
	if (confirm('Are you sure you want to delete?')) {
		dojo.xhrGet({
			url: '<?php echo uri("models/remove/"); ?>'+loc,
			load: function(data) {
				newrow = dojo.byId(loc);
				newrow.parentNode.removeChild(newrow);
				newrow = dojo.byId(loc+'-key');
				newrow.parentNode.removeChild(newrow);
			}
		});
	}
}
function new_field(loc) {
	dojo.xhrGet({
		url: '<?php echo uri("models/new/field/"); ?>'+loc,
		load: function (data) {
			dojo.byId(loc+'-fields').innerHTML += data;
		}
	});
}
function save_new_field(loc) {
	dojo.xhrPost({
		url: '<?php echo uri("models/get/field"); ?>',
		form: 'new_field_form',
		load: function(data) {
			cancel_new_field();
			dojo.byId(loc+'-fields').innerHTML += data;
		}
	});
}
function cancel_new_field() {
	var newrow = dojo.byId('new-field');
	newrow.parentNode.removeChild(newrow);
}
function edit_field(loc) {
	dojo.xhrGet({
		url: '<?php echo uri("models/edit/field/"); ?>'+loc,
		load: function(data) {
			dojo.byId(loc+'-key').innerHTML = data;
		}
	});
}
function save_edit_field(loc) {
	dojo.xhrPost({
		url: '<?php echo uri("models/get/field/"); ?>'+loc,
		form: 'edit_field_form',
		load: function(data) {
			dojo.byId(loc+'-key').innerHTML = data;
		}
	});
}
function cancel_edit_field(loc) {
	dojo.xhrGet({
		url: '<?php echo uri("models/get/field/"); ?>'+loc,
		load: function(data) {
			dojo.byId(loc+'-key').innerHTML = data;
		}
	});
}
function new_model() {
	dojo.xhrGet({
		url: '<?php echo uri("models/new/model"); ?>',
		load: function (data) {
			dojo.byId('models').innerHTML += data;
		}
	});
}
function save_new_model() {
	dojo.xhrPost({
		url: '<?php echo uri("models/get/model"); ?>',
		form: 'new_model_form',
		load: function(data) {
			cancel_new_model();
			dojo.byId('models').innerHTML += data;
		}
	});
}
function cancel_new_model() {
	var newrow = dojo.byId('new_model');
	newrow.parentNode.removeChild(newrow);
}
function delete_model(loc) {
	if (confirm('Are you sure you want to delete?')) {
		dojo.xhrGet({
			url: '<?php echo uri("models/remove/"); ?>'+loc,
			load: function(data) {
				newrow = dojo.byId(loc);
				newrow.parentNode.removeChild(newrow);
			}
		});
	}
}
function activate_model(loc, backup) {
	if ((backup) && !confirm("A backup model exists in the 'app/models/' directory. Press 'OK' to use this backup, or 'Cancel' to delete it and generate a new one.")) {
		dojo.byId("restore_backup").setAttribute('value', 0);
	}
	dojo.xhrPost({
		url: '<?php echo uri("models/get/model/"); ?>'+loc,
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
		url: '<?php echo uri("models/get/model/"); ?>'+prefix+'-'+loc,
		form: 'activate_'+prefix+'-'+loc,
		load: function(data) {
			var loc_node = dojo.byId(prefix);
			loc_node.innerHTML = data;
		}
	});
}
function deactivate_model(loc) {
	dojo.xhrPost({
		url: '<?php echo uri("models/get/model/"); ?>'+loc,
		form: 'deactivate_'+loc,
		load: function(data) {
			var loc_node = dojo.byId(loc);
			loc_node.setAttribute('class', 'inactive');
			loc_node.innerHTML = data;
		}
	});
}
</script>
