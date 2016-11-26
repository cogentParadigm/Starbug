<?php
//quick and dirty price formatter
//we will implement https://github.com/moneyphp/money when possible
namespace Starbug\Payment;
class PriceFormatter implements PriceFormatterInterface {
	protected $locale;
	protected $minorUnit;
	public function __construct($locale, $minorUnit) {
		$this->locale = $locale;
		$this->minorUnit = $minorUnit;
	}
	public function format($price) {
		setlocale(LC_MONETARY, $this->locale);
		return money_format('%.2n', $price/pow(10, $this->minorUnit));
	}
}
