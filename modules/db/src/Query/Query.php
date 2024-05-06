<?php
namespace Starbug\Db\Query;

use Starbug\Db\Query\Traits\Mode;
use Starbug\Db\Query\Traits\ValidationState;
use Starbug\Db\Query\Traits\Selection;
use Starbug\Db\Query\Traits\Tables;
use Starbug\Db\Query\Traits\Joins;
use Starbug\Db\Query\Traits\Conditions;
use Starbug\Db\Query\Traits\Parameters;
use Starbug\Db\Query\Traits\Group;
use Starbug\Db\Query\Traits\Sort;
use Starbug\Db\Query\Traits\Limit;
use Starbug\Db\Query\Traits\Set;
use Starbug\Db\Query\Traits\Exclusion;
use Starbug\Db\Query\Traits\Tagging;
use Starbug\Db\Query\Traits\Quotation;
use Starbug\Db\Query\Traits\Metadata;

final class Query implements QueryInterface {

  public function __construct($prefix = "", $identifierQuoteCharacter = "\"") {
    $this->prefix = $prefix;
    $this->identifierQuoteCharacter = $identifierQuoteCharacter;
  }

  use Mode;
  use ValidationState;
  use Selection;
  use Tables;
  use Joins;
  use Conditions;
  use Parameters;
  use Group;
  use Sort;
  use Limit;
  use Set;
  use Exclusion;
  use Tagging;
  use Quotation;
  use Metadata;
}
