<a href="<?php echo $this->url->build("subscriptions"); ?>" class="pull-right">Return to my subscriptions</a>
<h2>Make a Payment</h2>
<?php if ($this->db->success("subscriptions", "payment")) { ?>
	<div class="alert alert-success">Your payment has been processed successfully.</div>
<?php } ?>
<?php $now = date("Y-m-d H:i:s"); ?>
<div class="subscription">
	<p>
		<strong><?php echo $subscription["name"]; ?></strong>
		<?php if ($subscription["completed"]) { ?>
			<span class="label label-info">Completed</span>
		<?php } else if ($subscription["canceled"]) { ?>
			<span class="label label-danger">Canceled</span>
		<?php } else if ($subscription["active"]) { ?>
			<span class="label label-success">Active</span>
		<?php } else { ?>
			<span class="label label-warning">Suspended</span>
		<?php } ?>
	</p>
	<p><strong class="h1"><?php echo $this->priceFormatter->format($bill["amount"]); ?></strong></p>
	<p>
		<?php if ($bill["paid"]) { ?>
			<span class="label label-success">Paid</span>
		<?php } else if ($bill["due_date"] < $now) { ?>
			<span class="label label-danger">Past Due</span>
		<?php } else { ?>
			Due <?php echo date("l, F j", strtotime($bill["scheduled_date"])); ?>
		<?php } ?>
	</p>
</div>
<?php if (!$bill["paid"]) { ?>
<p class="alert alert-warning">Use the form below to submit a payment. To use a new card, select "Add a new card.." from the dropdown.</p>
<div class="row">
	<div class="col-sm-6">
		<?php $this->displays->render("BillPaymentForm", array("bill" => $bill["id"], "id" => $subscription["id"])); ?>
	</div>
</div>
<?php } ?>
