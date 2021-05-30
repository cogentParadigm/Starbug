<?php
// quick and dirty price formatter
// we will implement https://github.com/moneyphp/money when possible
namespace Starbug\Payment;

use NumberFormatter;

class PriceFormatter implements PriceFormatterInterface {
  protected $locale;
  protected $minorUnit;
  protected $formatter;
  public function __construct($locale, $minorUnit) {
    $this->locale = $locale;
    $this->minorUnit = $minorUnit;
    $this->formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
    $this->formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $this->minorUnit);
  }
  public function format($price) {
    return $this->formatter->format($price/pow(10, $this->minorUnit));
  }
}
