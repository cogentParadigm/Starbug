<?php
namespace Starbug\Db\Tests;

use Starbug\Db\Query\ExecutorInterface;
use Starbug\Db\Query\BuilderInterface;
use Starbug\Db\Query\QueryInterface;

class MockExecutor implements ExecutorInterface {

  /**
   * {@inheritDoc}
   */
  public function execute(BuilderInterface $builder) {
    // Empty method.
  }

  /**
   * {@inheritDoc}
   */
  public function count(BuilderInterface $builder, array $params = []) {
    // Empty method.
  }

  /**
   * {@inheritDoc}
   */
  public function validate(BuilderInterface $builder, $phase = self::PHASE_VALIDATION) {
    // Empty method.
  }

  public function interpolate(QueryInterface $query, $params = null) {
    // Empty method.
  }
}
