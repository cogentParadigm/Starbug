<?php echo '<'; ?>?xml version="1.0"?>
<getUnsettledTransactionListRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
	<merchantAuthentication>
		<name><?php echo $authnet->login_id; ?></name>
		<transactionKey><?php echo $authnet->transaction_key; ?></transactionKey>
	</merchantAuthentication>
</getUnsettledTransactionListRequest>
