---
name: testing
description: load this skill if you are asked anything about tests or testing, or if you need to run, create, or modify any tests.
---

## What I do

Guide writing and running PHPUnit tests for this project. I cover three kinds of tests: pure unit tests, database-backed tests, and web/shell acceptance tests.

All tests run through DDEV. See the `ddev` skill for environment details.

---

## Running tests

Always run PHPUnit inside the DDEV web container:

```bash
# Full suite
ddev exec vendor/bin/phpunit -c etc/phpunit.xml

# Single testsuite
ddev exec vendor/bin/phpunit -c etc/phpunit.xml --testsuite users

# Single file
ddev exec vendor/bin/phpunit -c etc/phpunit.xml modules/users/tests/Acceptance/AuthenticationTest.php

# Single method
ddev exec vendor/bin/phpunit -c etc/phpunit.xml --filter testListUsers modules/users/tests/Acceptance/Admin/UserManagementTest.php
```

---

## Picking a base class

| Base class | Extends | Use when |
|---|---|---|
| `PHPUnit\Framework\TestCase` | — | Pure unit tests: no database, no container, no fixtures |
| `Starbug\Db\Testing\DatabaseTestCase` | `TestCase` | Tests that need the DI container, database access, or YAML fixtures |
| `Starbug\Testing\WebTestCase` | `Starbug\Testing\BaseTestCase` | Web acceptance tests: in-process HTTP through the app middleware stack |
| `Starbug\Db\Testing\WebDatabaseTestCase` | `Starbug\Testing\WebTestCase` | Web acceptance tests that also load `#[Fixture]` data |

`DatabaseTestCase` lives in the `starbug/db` package (installed at `vendor/starbug/db`, source repo `~/public_html/starbug/db`). `WebTestCase` lives in `starbug/testing`.

---

## Database fixtures

Attach the `#[Fixture('path')]` attribute to the test class. `DatabaseTestCase::setUp()` auto-loads them before each test.

```php
use Starbug\Testing\Attribute\Fixture;
use Starbug\Db\Testing\DatabaseTestCase;

#[Fixture('app/fixtures/db/reset.yml')]
class UsersTest extends DatabaseTestCase {
```

Repeatable for multiple fixtures. The attribute is inherited by subclasses.

---

## Database assertion traits

These traits wrap common query-and-assert patterns. `DatabaseAssertions` and `CollectionAssertions` live in `Starbug\Db\Testing\Traits` inside `starbug/db`; `OperationAssertions` lives in `Starbug\Operation\Testing\Traits` inside `starbug/operation`.

### `DatabaseAssertions`

Direct query-and-assert helpers: `assertRecordExists`, `assertRecordNotExists`, `assertRecordCount`, `assertFieldValueContains`, `assertRecordMatches`, `assertRecordNotMatches`, `assertDatabaseErrors`, `assertRecordCreated`, `createRecord`.

### `CollectionAssertions`

For testing `CollectionInterface` implementations. Expose the collection under test as a `$collection` property (injected by the container), then use `query()`/`one()` to fetch and `assertQuery`/`assertQueryContains`/`assertQueryNotContains` to verify.

```php
use DI\Attribute\Inject;
use Starbug\Db\Testing\DatabaseTestCase;
use Starbug\Db\Testing\Traits\CollectionAssertions;
use Starbug\Users\Collection\AdminUsersCollection;

class AdminUsersCollectionTest extends DatabaseTestCase {
  use CollectionAssertions;

  #[Inject]
  protected AdminUsersCollection $collection;

  public function testFiltersByGroup(): void {
    $this->assertQueryContains([
      ['email' => 'root@example.com', 'deleted' => 'Active'],
    ], ['group' => 1]);
  }
}
```

Matching is field-subset (extra fields in actual rows are allowed) for `assertQueryContains` and `assertQueryNotContains`. `assertQuery` is strict full-set comparison.

### `OperationAssertions`

For testing `OperationInterface` implementations, from the `starbug/operation` package. `execute($class, $data, $config)` runs the operation; `assertExecuteSuccess`/`assertExecuteFailure` verifies the outcome.

```php
use Starbug\Db\Testing\DatabaseTestCase;
use Starbug\Operation\Testing\Traits\OperationAssertions;
use Starbug\Users\Operation\CreateUser;

class CreateUserTest extends DatabaseTestCase {
  use OperationAssertions;

  public function testCreatesUser(): void {
    $operation = $this->execute(CreateUser::class, [...]);
    $this->assertExecuteSuccess($operation);
  }
}
```

---

## Acceptance test architecture

Acceptance tests follow a 4-layer architecture. The rule is: **lower layers take concerns away from higher layers**.

| Layer | Lives in | Responsibility |
|---|---|---|
| **TestCase** | `modules/{name}/tests/Acceptance/` | The actual test methods. Can reach down to the driver for driver-specific assertions (e.g. "verify the New User button renders"). |
| **Domain** | `modules/{name}/tests/Domain/` | Composable domain operations with sensible defaults and built-in assertions. Only domain language: `createUser()`, `login()`, `assertOnUsersList()`. |
| **Adapter** | `modules/{name}/tests/Adapter/` | Translates domain actions into driver calls. Does the heavy lifting: form submission, navigation, parsing results. Implements an interface so the domain stays transport-agnostic. |
| **Driver** | `starbug/testing` (`DirectDriver`, `ShellDriver`) | Transport primitives: `get()`, `post()`, `submitForm()`, `filter()`, `run()`, etc. |

The adapter should assert the results of domain actions whenever possible. The test case or domain should not need to operate the driver for routine assertions.

See `references/four-layer-architecture.md` for a full worked example and guidance on what belongs in each layer.

---

## DI wiring

The container auto-loads `modules/{name}/tests/etc/di.php` for every enabled module. Bind adapter interfaces to concrete implementations there:

```php
// modules/users/tests/etc/di.php
return [
  AuthAdapterInterface::class => autowire(AuthWebAdapter::class),
  UserManagementAdapterInterface::class => autowire(UserManagementWebAdapter::class),
];
```

Then inject the domain objects into your test case with `#[Inject]`:

```php
#[Inject]
protected Authentication $auth;

#[Inject]
protected UserManagement $users;
```

---

## Shell acceptance tests

Use `Starbug\Testing\ShellDriver` for CLI-based tests. The domain and adapter pattern is the same as web tests; only the transport changes.

> Note: Shell tests currently require manual wiring (`new ShellDriver()`, `new ShellAdapter($driver)`, `new Domain($adapter)`) instead of container injection. This is temporary and will be replaced by container auto-wiring. See `references/driver-capabilities.md` for current usage.

---

## Per-package phpunit

The `starbug/db` package keeps its own `phpunit.xml` in its repository (`~/public_html/starbug/db`), covering the query builder and the testing traits against `MockDatabase` + `Builder` + `Compiler` + `Schema`. Its `autoload-dev` maps `Starbug\Db\Tests\` to `tests/`, which the root install does not inherit, so it runs from its own `vendor/`.

```bash
cd ~/public_html/starbug/db
composer update --ignore-platform-req=ext-yaml --no-security-blocking
vendor/bin/phpunit -c phpunit.xml
```

Both flags are host-specific: the host PHP lacks `ext-yaml` (required by `starbug/importer`, unused by these tests) and host Composer blocks advisories in the existing tree.

---

## References

- `references/four-layer-architecture.md` — detailed layer breakdown with code examples
- `references/driver-capabilities.md` — `WebDriverInterface` and `WebAssertions` reference
- `references/skill-maintenance.md` — pending implementations that will require updating this skill
