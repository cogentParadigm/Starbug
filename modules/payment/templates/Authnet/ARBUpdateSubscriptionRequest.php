<?php echo '<'; ?>?xml version="1.0"?>
<ARBUpdateSubscriptionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
  <merchantAuthentication>
    <name><?php echo $authnet->login_id; ?></name>
    <transactionKey><?php echo $authnet->transaction_key; ?></transactionKey>
  </merchantAuthentication>
<?php if (isset($authnet->refId)) { ?>
  <refId><?php echo $authnet->refId; ?></refId>
<?php } ?>
	<subscriptionId><?php echo $authnet->subscriptionId; ?></subscriptionId>
  <subscription>
<?php if (isset($authnet->subscription_name)) { ?>
    <name><?php echo $authnet->subscription_name; ?></name>
<?php } ?>
<?php if (isset($authnet->start_date) || isset($authnet->total_occurrences) || isset($authnet->trial_occurrences)) { ?>
    <paymentSchedule>
<?php if (isset($authnet->start_date)) { ?>
      <startDate><?php echo $authnet->start_date; ?></startDate>
<?php } ?>
<?php if (isset($authnet->total_occurrences)) { ?>
      <totalOccurrences><?php echo $authnet->total_occurrences; ?></totalOccurrences>
<?php } ?>
<?php if (isset($authnet->trial_occurrences)) { ?>
      <trialOccurrences><?php echo $authnet->trial_occurrences; ?></trialOccurrences>
<?php } ?>
    </paymentSchedule>
<?php } ?>
<?php if (isset($authnet->amount)) { ?>
    <amount><?php echo $authnet->amount; ?></amount>
<?php } ?>
<?php if (isset($authnet->trial_amount)) { ?>
    <trialAmount><?php echo $authnet->trial_amount; ?></trialAmount>
<?php } ?>
<?php if (isset($authnet->card_number)) { ?>
    <payment>
      <creditCard>
        <cardNumber><?php echo $authnet->card_number; ?></cardNumber>
        <expirationDate><?php echo $authnet->expiration_date; ?></expirationDate>
        <cardCode><?php echo $authnet->card_code; ?></cardCode>
      </creditCard>
    </payment>
<?php } ?>
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
<?php if (isset($authnet->first_name) && isset($authnet->last_name)) { ?>
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
<?php } ?>
  </subscription>
</ARBUpdateSubscriptionRequest>
