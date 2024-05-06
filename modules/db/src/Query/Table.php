<?php
namespace Starbug\Db\Query;

/**
 * Represents a table within the FROM/JOIN clauses of an SQL query.
 */
class Table extends Condition {
  /**
   * The table name.
   *
   * @var string
   */
  protected $table;
  /**
   * The alias to use for this table.
   *
   * @var string
   */
  protected $alias;
  /**
   * The type of join (use Starbug\Db\Query\JoinType constants).
   *
   * @var string|false
   */
  protected $joinType = false;
  /**
   * The type of relationship (use Starbug\Db\Query\RelationshipType constants).
   *
   * @var string|false
   */
  protected $relationshipType = false;
  /**
   * The column referencing this table.
   * For example, if this table might be users
   * reference might be pages.owner
   *
   * @var string|false
   */
  protected $reference = false;
  /**
   * Conditions resulting from $relationshipType and $reference.
   *
   * @var array
   */
  protected $relationshipConditions = [];
  /**
   * Keeps track of whether $relationshipConditions needs to be rebuilt.
   * This should be set to true whenever any of the other properties above are changed.
   *
   * @var boolean
   */
  protected $dirty = false;

  /**
   * Instantiate a new Table within a query.
   *
   * @param QueryInterface $query The query that this table belongs to.
   * @param string $table The name of the table.
   * @param string|false $alias An optional alias for the table.
   */
  public function __construct(
    protected QueryInterface $query,
    $table,
    $alias = false
  ) {
    $this->query = $query;
    $this->table = $table;
    $this->alias = (false == $alias) ? $table : $alias;
  }
  /**
   * Add a condition to the ON clause joining this table.
   *
   * @param string $condition A conditional expression.
   * @param array $ops An array of options for the condition.
   *
   * @return static This instance to allow method chaining.
   */
  public function on($condition, array $ops = []) {
    return $this->where($condition, $ops);
  }
  /**
   * Set the join type. eg. JoinType::LEFT
   *
   * @param string $type The join type.
   *
   * @return static This instance to allow method chaining.
   */
  public function setType($type) {
    $this->joinType = $type;
    return $this;
  }
  public function to($relationshipType) {
    $this->relationshipType = $relationshipType;
    $this->dirty = true;
    return $this;
  }
  public function via($column) {
    $this->reference = $column;
    $this->dirty = true;
    return $this;
  }
  public function setAlias($alias) {
    $this->alias = $alias;
    $this->dirty = true;
    return $this;
  }
  public function getName() {
    return $this->table;
  }
  public function getAlias() {
    return $this->alias;
  }
  public function getJoinType() {
    return $this->joinType;
  }
  public function getRelationshipType() {
    return $this->relationshipType;
  }
  public function getReference() {
    return $this->reference;
  }
  public function getConditions() {
    $this->buildRelationshipConditions();
    return array_merge($this->relationshipConditions, $this->conditions);
  }
  public function count(): int {
    $this->buildRelationshipConditions();
    return count($this->relationshipConditions) + count($this->conditions);
  }
  protected function buildRelationshipConditions() {
    if ($this->dirty) {
      if ($this->relationshipType == RelationshipType::ONE) {
        $this->relationshipConditions = [["condition" => $this->alias.".id=".$this->reference]];
      } elseif ($this->relationshipType == RelationshipType::MANY) {
        $baseAlias = $this->reference;
        $baseTable = $this->query->getTable($baseAlias)->getName();
        $this->relationshipConditions = [["condition" => $this->alias.".".$baseTable."_id=".$baseAlias.".id"]];
      }
      $this->dirty = false;
    }
  }
}
