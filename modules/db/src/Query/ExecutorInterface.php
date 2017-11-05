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
  public function validate(BuilderInterface $builder, int $phase = self::PHASE_VALIDATION);

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
   * Return the database connection being used.
   *
   * @return Starbug\Core\DatabaseInterface The connection.
   */
  public function getConnection();
}
