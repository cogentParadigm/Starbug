<?php
namespace Starbug\Db\Query;

interface ExecutorInterface {
  const PHASE_VALIDATION = 0;
  const PHASE_STORE = 1;
  const PHASE_AFTER_STORE = 2;
  const PHASE_BEFORE_DELETE = 3;
  const PHASE_AFTER_DELETE = 4;

  /**
   * Validate a write.
   *
   * @param BuilderInterface $builder The query builder.
   * @param integer $phase The validation phase. Must be one of the PHASE_* constant values above.
   */
  public function validate(BuilderInterface $builder, $phase = self::PHASE_VALIDATION);

  /**
   * Execute the query and get back the rows.
   *
   * @param BuilderInterface $builder The query builder.
   *
   * @return mixed The records returned by a SELECT query, otherwise the affected row count.
   */
  public function execute(BuilderInterface $builder);

  /**
   * Execute a count query derived from the passed in query and return the count.
   *
   * @param BuilderInterface $builder The query builder.
   * @param array $params Optional parameters.
   *
   * @return integer The row count.
   */
  public function count(BuilderInterface $builder, array $params = []);

  /**
   * Replaces any parameter placeholders in a query with the value of that
   * parameter. Useful for debugging. Assumes anonymous parameters from
   * $params are are in the same order as specified in $query
   *
   * @param string $query The sql query with parameter placeholders
   * @param array $params The array of substitution parameters
   *
   * @return string The interpolated query
   */
  public function interpolate(QueryInterface $query, $params = null);
}
