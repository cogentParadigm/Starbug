<?php echo '<'; ?>?xml version="1.0"?>
<getSettledBatchListRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
	<merchantAuthentication>
		<name><?php echo $authnet->login_id; ?></name>
		<transactionKey><?php echo $authnet->transaction_key; ?></transactionKey>
	</merchantAuthentication>
<?php if (isset($authnet->includeStatistics)) { ?>
	<includeStatistics><?php echo $authnet->includeStatistics; ?></includeStatistics>
<?php } ?>
<?php if (isset($authnet->firstSettlementDate)) { ?>
	<firstSettlementDate><?php echo $authnet->firstSettlementDate; ?></firstSettlementDate>
<?php } ?>
<?php if (isset($authnet->lastSettlementDate)) { ?>
	<lastSettlementDate><?php echo $authnet->lastSettlementDate; ?></lastSettlementDate>
<?php } ?>
</getSettledBatchListRequest>
