<?php
	if ($address && !$edit) {
		echo $formatted_address;
		echo '<br/><br/>';
		echo '<a href="javascript:;" class="edit btn btn-default">Edit</a>';
		echo '<input type="hidden" class="address-value" value="'.$address['id'].'"/>';
	} else {
		$this->displays->render("AddressForm", $options);
	}
?>
