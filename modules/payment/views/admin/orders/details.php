<div class="panel panel-default">
	<div class="panel-heading"><strong> <span>Order #<?php echo $id; ?></span></strong></div>
	<div class="panel-body">
		<?php
			//product_lines::query_order
			$this->displays->render("ProductLinesGrid", array("id" => $id, 'attributes' => array('base_url' => 'admin/orders')));
		?>
		<br/>
		<?php
			//$this->displays->render("ShippingLinesGrid", array("id" => $id, 'attributes' => array('base_url' => 'admin/orders')));
		?>
		<br/>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Contact Information</h3>
			</div>
			<div class="panel-body">
				<?php
					echo $order['email']."<br/>";
					echo $order['phone'];
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Shipping Address</h3>
			</div>
			<div class="panel-body">
				<?php
					echo $this->models->get("address")->format($order['shipping_address']);
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Billing Address</h3>
			</div>
			<div class="panel-body">
				<?php
					echo $this->models->get("address")->format($order['billing_address']);
				?>
			</div>
		</div>
	</div>
</div>
		<div clas="totals" style="margin:20px 0">
			<div class="total">Subtotal: <strong><?php echo money_format('%.2n', $products['total']/100); ?></strong></div>
			<div class="total">Shipping: <strong><?php echo money_format('%.2n', $shipping['total']/100); ?></strong></div>
			<div class="total">Total: <strong><?php echo money_format('%.2n', ($products['total'] + $shipping['total'])/100); ?></strong></div>
		</div>

	</div>
</div>
