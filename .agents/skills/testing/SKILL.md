---
name: testing
description: How to write and run tests for this Starbug application.
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
| `Starbug\Testing\DatabaseTestCase` | `TestCase` | Tests that need the DI container, database access, or YAML fixtures |
| `Starbug\Testing\WebTestCase` | `DatabaseTestCase` | Web acceptance tests: in-process HTTP through the app middleware stack |

`DatabaseTestCase` and `WebTestCase` live in the `starbug/testing` package (path repo at `~/public_html/starbug/testing/`).

---

## Database fixtures

Attach the `#[Fixture('path')]` attribute to the test class. `DatabaseTestCase::setUp()` auto-loads them before each test.

```php
use Starbug\Testing\Attribute\Fixture;
use Starbug\Testing\DatabaseTestCase;

#[Fixture('app/fixtures/db/reset.yml')]
class UsersTest extends DatabaseTestCase {
```

Repeatable for multiple fixtures. The attribute is inherited by subclasses.

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

## References

- `references/four-layer-architecture.md` — detailed layer breakdown with code examples
- `references/driver-capabilities.md` — `WebDriverInterface` and `WebAssertions` reference
- `references/skill-maintenance.md` — pending implementations that will require updating this skill
