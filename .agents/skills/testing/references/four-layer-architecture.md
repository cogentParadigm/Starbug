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
- Driver-specific assertions that don't belong in the domain or adapter (e.g. "verify the CSV export contains the expected headers")
- `#[Fixture()]` attributes

**What does NOT belong here:**
- Low-level HTTP or CLI calls
- Parsing HTML or JSON
- Form field names or CSRF handling
- URL paths
- Assertions about domain-specific UI elements (links, buttons, grid columns) — those belong in the adapter

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

  // Direct driver use for a driver-specific check that is not part of a routine domain flow
  public function testCsvExportContainsExpectedHeaders(): void {
    $csv = $this->getDriver()->download('api/users/admin.csv');
    $this->assertStringContainsString('First Name', $csv);
    $this->assertStringContainsString('Last Name', $csv);
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
- URL paths and route names (app-relative, without leading slash)
- Form submission logic
- Parsing responses for domain-relevant data
- Asserting the results of domain actions

**What does NOT belong here:**
- Domain-level defaults or orchestration (that belongs in Domain)
- Test-case-specific assertions

> **Path convention:** Adapters pass app-relative paths to the driver (e.g. `admin/users/create`). The driver resolves them against `baseUrl + basePath` per RFC 3986. A leading slash (e.g. `/admin/users/create`) means domain-absolute and bypasses `basePath`.

```php
class UserManagementWebAdapter extends WebAdapter implements UserManagementAdapterInterface {
  public function createUser(array $data): void {
    $this->driver->submitForm('admin/users/create', $data);
  }

  public function assertUserCreated(): void {
    $this->assertPathEquals('/admin/users');
  }

  public function assertOnUsersList(): void {
    $this->assertPathEquals('/admin/users');
    $this->assertPageContains('New User');
    $this->assertLinkHrefEquals('New User', '/admin/users/create');
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
| "Page contains text 'New User'" | Adapter (`assertOnUsersList()`) |
| "Link 'New User' points to /admin/users/create" | Adapter (`assertOnUsersList()`) |
| "Button with class .btn-primary exists" | TestCase (`assertElementExists()`) |
| "Form field 'email' shows 'required' error" | Adapter (`assertValidationFailed()`) or Domain |

The lower the layer can handle it, the better. The test case should not be parsing HTML for routine domain flows. Domain-specific UI assertions (link text, hrefs, page content tied to a domain view) belong in the adapter so they are validated for every test that exercises that view.

## Why Interfaces?

Adapters implement interfaces so the domain layer is transport-agnostic. A `UserManagementAdapterInterface` can be implemented by:

- `UserManagementWebAdapter` (uses `DirectDriver` — in-process HTTP)
- A future `UserManagementBrowserAdapter` (would use a browser driver for JS tests)
- `UserManagementShellAdapter` (uses `ShellDriver` — CLI commands)

The domain class type-hints the interface and never knows which transport is in use.
