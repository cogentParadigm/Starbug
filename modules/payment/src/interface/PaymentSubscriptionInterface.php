<?php
namespace Starbug\Payment;
/**
 * a simple interface for creating payments
 */
interface PaymentSubscriptionInterface {
	public function create($order, $subscription);
	public function update($order, $subscription);
	public function cancel($order, $subscription);
	public function status($id);
}
