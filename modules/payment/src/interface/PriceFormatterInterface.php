<?php
//temporary interface for formatting prices.
//we will implement https://github.com/moneyphp/money when possible
namespace Starbug\Payment;
interface PriceFormatterInterface {
	public function format($price);
}
