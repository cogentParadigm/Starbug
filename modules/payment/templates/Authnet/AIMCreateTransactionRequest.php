<?php echo '<'; ?>?xml version="1.0"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
  <merchantAuthentication>
    <name><?php echo $authnet->login_id; ?></name>
    <transactionKey><?php echo $authnet->transaction_key; ?></transactionKey>
  </merchantAuthentication>
<?php if (isset($authnet->refId)) { ?>
  <refId><?php echo $authnet->refId; ?></refId>
<?php } ?>
  <transactionRequest>
    <transactionType><?php echo $authnet->type; ?></transactionType>
    <amount><?php echo $authnet->amount; ?></amount>
    <payment>
      <creditCard>
        <cardNumber><?php echo $authnet->card_number; ?></cardNumber>
        <expirationDate><?php echo $authnet->expiration_date; ?></expirationDate>
        <cardCode><?php echo $authnet->card_code; ?></cardCode>
      </creditCard>
    </payment>
<?php if (isset($authnet->email) || isset($authnet->phone)) { ?>
		<customer>
<?php if (isset($authnet->email)) { ?>
			<email><?php echo $authnet->email; ?></email>
<?php } ?>
<?php if (isset($authnet->phone)) { ?>
			<phoneNumber><?php echo $authnet->phone; ?></phoneNumber>
<?php } ?>
		</customer>
<?php } ?>
    <billTo>
      <firstName><?php echo $authnet->first_name; ?></firstName>
      <lastName><?php echo $authnet->last_name; ?></lastName>
<?php
	foreach(array("company", "address", "city", "state", "zip", "country") as $field) {
		if (isset($authnet->{$field})) {
?>
			<<?php echo $field; ?>><?php echo $authnet->{$field}; ?></<?php echo $field; ?>>
<?php
		}
	}
?>
    </billTo>
  </transactionRequest>
</createTransactionRequest>