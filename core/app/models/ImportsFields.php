<?php
/**
 * imports_fields model
 * @ingroup models
 */
namespace Starbug\Core;
class ImportsFields extends ImportsFieldsModel {

	public $label_select = "CONCAT(imports_fields.source, ' => ', imports_fields.destination, CASE WHEN update_key=1 THEN ' (update key)' ELSE '' END)";

}
?>
