<?php
namespace Starbug\Users\Tests\Operation;

use DI\Attribute\Inject;
use Starbug\Db\Testing\DatabaseTestCase;
use Starbug\Operation\Testing\Traits\OperationAssertions;
use Starbug\Emails\MailerInterface;
use Starbug\Emails\MockMailer;
use Starbug\Testing\Attribute\Bind;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Users\Operation\CreateUser;

#[Bind(MailerInterface::class, MockMailer::class)]
#[Fixture('app/fixtures/db/reset.yml')]
class CreateUserTest extends DatabaseTestCase {
  use OperationAssertions;

  #[Inject]
  protected MailerInterface $mailer;

  protected function setUp(): void {
    parent::setUp();
    if ($this->mailer instanceof MockMailer) {
      $this->mailer->clear();
    }
  }

  public function testCreatesUser(): void {
    $operation = $this->execute(CreateUser::class, [
      'first_name' => 'Test',
      'last_name' => 'User',
      'email' => 'test@example.com',
      'password' => 'Wj6tNdQP3bnc22IQ',
      'groups' => 'user'
    ]);
    $this->assertExecuteSuccess($operation);
    $this->assertRecordExists('users', ['email' => 'test@example.com']);
  }

  public function testSendsAccountCreationEmail(): void {
    $operation = $this->execute(CreateUser::class, [
      'first_name' => 'Test',
      'last_name' => 'User',
      'email' => 'test@example.com',
      'password' => 'Wj6tNdQP3bnc22IQ',
      'groups' => 'user'
    ]);
    $this->assertExecuteSuccess($operation);
    $this->assertInstanceOf(MockMailer::class, $this->mailer);
    $last = $this->mailer->getLastMessage();
    $this->assertNotNull($last);
    $this->assertEquals('Account Creation', $last['options']['template']);
    $this->assertEquals('test@example.com', $last['options']['to']);
    $this->assertEquals('Wj6tNdQP3bnc22IQ', $last['data']['user']['password']);
  }

  public function testReturnsErrorsForInvalidData(): void {
    $operation = $this->execute(CreateUser::class, [
      'email' => '',
      'password' => ''
    ]);
    $this->assertExecuteFailure($operation);
    $this->assertDatabaseErrors([
      'email' => 'required',
      'password' => 'required'
    ]);
  }

  public function testRejectsWeakPassword(): void {
    $operation = $this->execute(CreateUser::class, [
      'first_name' => 'Test',
      'last_name' => 'User',
      'email' => 'test@example.com',
      'password' => 'password123'
    ]);
    $this->assertExecuteFailure($operation);
    $this->assertDatabaseErrors([
      'password' => 'common password'
    ]);
  }

  public function testRejectsDuplicateEmail(): void {
    $this->execute(CreateUser::class, [
      'first_name' => 'First',
      'last_name' => 'User',
      'email' => 'duplicate@example.com',
      'password' => 'Wj6tNdQP3bnc22IQ',
      'groups' => 'user'
    ]);

    $operation = $this->execute(CreateUser::class, [
      'first_name' => 'Second',
      'last_name' => 'User',
      'email' => 'duplicate@example.com',
      'password' => 'Wj6tNdQP3bnc22IQ',
      'groups' => 'user'
    ]);
    $this->assertExecuteFailure($operation);
    $this->assertDatabaseErrors([
      'email' => 'already exists'
    ]);
  }

  public function testRejectsMismatchedPassword(): void {
    $operation = $this->execute(CreateUser::class, [
      'first_name' => 'Test',
      'last_name' => 'User',
      'email' => 'test@example.com',
      'password' => 'Wj6tNdQP3bnc22IQ',
      'password_confirm' => 'different-password'
    ]);
    $this->assertExecuteFailure($operation);
    $this->assertDatabaseErrors([
      'password' => 'do not match'
    ]);
  }
}
