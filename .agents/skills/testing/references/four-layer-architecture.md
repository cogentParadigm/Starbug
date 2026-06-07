# Four-Layer Architecture for Acceptance Tests

This is the architecture used for acceptance tests in this project. It is based on Dave Farley's 4-layer model, adapted to Starbug's module system.

## Directory Layout

```
modules/{name}/
  tests/
    Acceptance/
      Admin/
        UserManagementTest.php
      AuthenticationTest.php
    Domain/
      Authentication.php
      UserManagement.php
    Adapter/
      AuthAdapterInterface.php
      AuthWebAdapter.php
      UserManagementAdapterInterface.php
      UserManagementWebAdapter.php
    etc/
      di.php
```

## Layer Responsibilities

### Layer 1: TestCase

Implements the actual tests. It can go directly to the driver for driver-specific validation, but for routine domain flows it should use the domain layer.

**What belongs here:**
- Orchestrating domain actions into test scenarios
- Driver-specific assertions that don't belong in the domain ("verify the New User button renders")
- `#[Fixture()]` attributes

**What does NOT belong here:**
- Low-level HTTP or CLI calls
- Parsing HTML or JSON
- Form field names or CSRF handling
- URL paths

```php
#[Fixture('app/fixtures/db/reset.yml')]
class UserManagementTest extends WebTestCase {
  #[Inject]
  protected Authentication $auth;

  #[Inject]
  protected UserManagement $users;

  public function testCreateUser(): void {
    $this->auth->login('root', 'Mz2&cssMy@9tclan');
    $this->users->createUser([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);
    $this->users->assertUserCreated();
    $this->users->assertOnUsersList();
  }

  // Direct driver use for a driver-specific check
  public function testButtonRenders(): void {
    $this->getDriver()->get('/admin/users');
    $this->assertElementExists('a[href="/admin/users/create"]');
  }
}
```

### Layer 2: Domain

A thin, composable layer that test cases call to perform domain actions. It should only implement things that belong in the domain.

**What belongs here:**
- Domain operations: `createUser()`, `login()`, `logout()`, `listUsers()`
- Sensible defaults for test data (e.g. auto-filling `password_confirm`)
- Domain-level assertions: `assertUserCreated()`, `assertOnUsersList()`, `assertLoggedIn()`

**What does NOT belong here:**
- HTML parsing, CSS selectors, form field names
- URL paths
- HTTP method calls (`GET`, `POST`)

```php
class UserManagement {
  public function __construct(
    private UserManagementAdapterInterface $adapter
  ) {}

  public function createUser(array $data): void {
    $defaults = ['password_confirm' => $data['password'] ?? ''];
    $this->adapter->createUser($data + $defaults);
  }

  public function assertUserCreated(): void {
    $this->adapter->assertUserCreated();
  }
}
```

### Layer 3: Adapter

The translation layer. It translates domain actions into driver actions and should do as much heavy lifting as possible so the domain stays light.

**What belongs here:**
- URL paths and route names
- Form submission logic
- Parsing responses for domain-relevant data
- Asserting the results of domain actions

**What does NOT belong here:**
- Domain-level defaults or orchestration (that belongs in Domain)
- Test-case-specific assertions

```php
class UserManagementWebAdapter extends WebAdapter implements UserManagementAdapterInterface {
  public function createUser(array $data): void {
    $this->driver->submitForm('/admin/users/create', $data);
  }

  public function assertUserCreated(): void {
    Assert::assertEquals('/admin/users', $this->driver->getCurrentPath());
  }
}
```

### Layer 4: Driver

Provides an API to interact with different protocols (web, shell). It should do as much as possible on behalf of the adapters.

**What belongs here:**
- Generic transport primitives: `get()`, `post()`, `submitForm()`, `filter()`, `followLink()`
- Cookie/session management
- Redirect following
- DOM parsing helpers

**What does NOT belong here:**
- Domain concepts ("create a user")
- Application-specific HTML structure

See `driver-capabilities.md` for the full method reference.

## Guideline: Where Do Assertions Go?

| Assertion type | Belongs in |
|---|---|
| "User was created in the database" | Domain (`assertUserCreated()`) |
| "Current path is /admin/users" | Adapter (`assertOnUsersList()`) or Domain |
| "Page contains text 'New User'" | TestCase (`assertPageContains()`) |
| "Button with class .btn-primary exists" | TestCase (`assertElementExists()`) |
| "Form field 'email' shows 'required' error" | Adapter (`assertValidationFailed()`) or Domain |

The lower the layer can handle it, the better. The test case should not be parsing HTML for routine domain flows.

## Why Interfaces?

Adapters implement interfaces so the domain layer is transport-agnostic. A `UserManagementAdapterInterface` can be implemented by:

- `UserManagementWebAdapter` (uses `DirectDriver` — in-process HTTP)
- A future `UserManagementBrowserAdapter` (would use a browser driver for JS tests)
- `UserManagementShellAdapter` (uses `ShellDriver` — CLI commands)

The domain class type-hints the interface and never knows which transport is in use.
