<?php echo '<'; ?>?xml version="1.0"?>
<ARBGetSubscriptionStatusRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
  <merchantAuthentication>
    <name><?php echo $authnet->login_id; ?></name>
    <transactionKey><?php echo $authnet->transaction_key; ?></transactionKey>
  </merchantAuthentication>
<?php if (isset($authnet->refId)) { ?>
  <refId><?php echo $authnet->refId; ?></refId>
<?php } ?>
  <subscriptionId><?php echo $authnet->subscriptionId; ?></subscriptionId>
</ARBGetSubscriptionStatusRequest>
