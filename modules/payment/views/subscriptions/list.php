<?php if (empty($subscriptions)) { ?>
	<p>You have no subscriptions.</p>
<?php } else { ?>
	<?php $now = date("Y-m-d H:i:s"); ?>
	<?php foreach ($subscriptions as $s) { ?>
		<div class="subscription">
			<a class="btn btn-info pull-right" href="<?php echo $this->url->build("subscriptions/update/".$s["id"]); ?>">Update</a>
			<p>
				<strong><?php echo $s["name"]; ?></strong>
				<?php if ($s["completed"]) { ?>
					<span class="label label-info">Completed</span>
				<?php } else if ($s["canceled"]) { ?>
					<span class="label label-danger">Canceled</span>
				<?php } else if ($s["active"]) { ?>
					<span class="label label-success">Active</span>
				<?php } else { ?>
					<span class="label label-warning">Suspended</span>
				<?php } ?>
			</p>
			<?php foreach ($s["bills"] as $bill) { ?>
				<p><strong class="h1"><?php echo $this->priceFormatter->format($bill["amount"]); ?></strong></p>
				<p>
					<?php if ($bill["due_date"] < $now) { ?>
						<span class="label label-danger">Past Due</span> <a href="<?php echo $this->url->build("subscriptions/payment/".$bill["id"]); ?>">Make a payment</a>
					<?php } else { ?>
						Due <?php echo date("l, F j", strtotime($bill["scheduled_date"])); ?>
					<?php } ?>
				</p>
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>
