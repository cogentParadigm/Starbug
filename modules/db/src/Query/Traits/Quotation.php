<?php
namespace Starbug\Db\Query\Traits;

trait Quotation {
  protected $identifierQuoteCharacter = "\"";

  public function quoteIdentifier($str) {
    $char = $this->getIdentifierQuoteCharacter();
    return $char . $str . $char;
  }

  public function getIdentifierQuoteCharacter() {
    return $this->identifierQuoteCharacter;
  }

  public function setIdentifierQuoteCharacter($char) {
    $this->identifierQuoteCharacter = $char;
  }
}
