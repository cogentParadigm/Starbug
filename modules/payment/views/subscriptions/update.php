<a href="<?php echo $this->url->build("subscriptions"); ?>" class="pull-right">Return to my subscriptions</a>
<h2>Update Subscription</h2>
<?php if ($this->db->success("subscriptions", "update")) { ?>
	<div class="alert alert-success">Your subscription has been updated successfully.</div>
<?php } ?>
<div data-dojo-type="payment/Order" data-dojo-props="query:{order:<?php echo $subscription["orders_id"]; ?>}"></div>
<div style="clear:both"></div>
<p>Use the form below to change the credit or debit card associated with this subscription. To add a new card, select "Add a new card.." from the dropdown. This will simply update your card on file. The new card will not be charged until your next due date.</p>
<div class="row">
	<div class="col-sm-6">
<?php $this->displays->render("UpdateSubscriptionForm", array("id" => $subscription["id"])); ?>
	</div>
</div>
