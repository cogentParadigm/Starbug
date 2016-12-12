<?php
namespace Starbug\Payment;
/**
 * a simple interface for creating payments
 */
interface PaymentInterface {
	public function create($order, $payment);
}
