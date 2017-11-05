<?php
namespace Starbug\Db\Tests;

use Starbug\Db\Query\ExecutorInterface;
use Starbug\Db\Query\BuilderInterface;

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
  public function getConnection() {
    // Empty method.
  }

  /**
   * {@inheritDoc}
   */
  public function validate(BuilderInterface $builder, int $phase = self::PHASE_VALIDATION) {
    // Empty method
  }

}
