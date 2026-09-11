<?php
namespace Starbug\Db\Testing\Traits;

use DI\Attribute\Inject;
use PHPUnit\Framework\Assert;
use Starbug\Operation\OperationFactoryInterface;
use Starbug\Operation\OperationInterface;

/**
 * Operation execution and assertion helpers for PHPUnit tests.
 *
 * The test class calls `execute()` to run an operation through the injected
 * factory, then `assertExecuteSuccess()` / `assertExecuteFailure()` to
 * verify the outcome. `OperationFactoryInterface` is injected by the test
 * container, mirroring the rest of the trait surface.
 */
trait OperationAssertions {

  #[Inject]
  protected OperationFactoryInterface $operationFactory;

  /**
   * Execute an operation and return the operation instance.
   *
   * @param string $operationClass Fully-qualified operation class name.
   * @param array $data Input data for the operation.
   * @param array $config Configuration passed to operation->configure().
   *
   * @return OperationInterface The executed operation instance.
   */
  protected function execute(string $operationClass, array $data, array $config = []): OperationInterface {
    $operation = $this->operationFactory->get($operationClass);
    if (!empty($config)) {
      $operation->configure($config);
    }
    $operation->execute($data);
    return $operation;
  }

  /**
   * Assert an operation succeeded.
   */
  protected function assertExecuteSuccess(OperationInterface $operation, ?string $message = null): void {
    Assert::assertTrue($operation->success(), $message ?? "Expected operation to succeed.");
  }

  /**
   * Assert an operation failed.
   */
  protected function assertExecuteFailure(OperationInterface $operation, ?string $message = null): void {
    Assert::assertTrue($operation->failure(), $message ?? "Expected operation to fail.");
  }
}
