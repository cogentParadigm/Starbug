<?php
namespace Starbug\Db\Query;

class Query implements QueryInterface {

  public function __construct($prefix = "", $identifierQuoteCharacter = "\"") {
    $this->prefix = $prefix;
    $this->identifierQuoteCharacter = $identifierQuoteCharacter;
  }

  use Traits\Mode;
  use Traits\ValidationState;
  use Traits\Selection;
  use Traits\Tables;
  use Traits\Joins;
  use Traits\Conditions;
  use Traits\Parameters;
  use Traits\Group;
  use Traits\Sort;
  use Traits\Limit;
  use Traits\Set;
  use Traits\Exclusion;
  use Traits\Tagging;
  use Traits\Quotation;
  use Traits\Metadata;
}
