<?php
namespace Starbug\Payment;
/**
 * a simple interface for creating payments
 */
interface PaymentSubscriptionInterface {
	public function create($order, $subscription);
	public function update($subscription);
	public function cancel($subscription);
	public function status($subscription);
}
