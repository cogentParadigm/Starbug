# Driver Capabilities Reference

The `starbug/testing` package provides two primary drivers: `DirectDriver` (in-process HTTP) and `ShellDriver` (CLI execution). Web acceptance tests use `DirectDriver` by default. Shell acceptance tests use `ShellDriver`.

## DirectDriver

Dispatches HTTP requests through the application middleware stack in-process, without an actual HTTP client or web server. Maintains cookie state via a shared jar and auto-follows redirects up to 5 hops.

### Navigation

Paths are resolved per RFC 3986 against the app base (`baseUrl` + `basePath`).
App-relative paths should **not** include a leading slash.

```php
$driver->get('login');              // App-relative GET request
$driver->post('login', $data);     // App-relative POST request
$driver->request('DELETE', '/api/users/1'); // Domain-absolute (leading slash)
```

### Form Interaction

```php
// Bulk: GET the page, parse the form, set values, submit.
// Hidden inputs (including CSRF oid) travel automatically.
$driver->submitForm('admin/users/create', [
  'first_name' => 'Dave',
  'email' => 'dave@neonrain.com'
]);

// Step-by-step field interaction
$driver->get('admin/users/create');
$driver->fillField('first_name', 'Dave');
$driver->fillField('email', 'dave@neonrain.com');
$driver->pressButton('Save');

// Checkbox and select
$driver->checkField('is_admin');
$driver->selectOption('status', 'active');
```

### Link Navigation

```php
$driver->get('admin/users');
$link = $driver->selectLink('New User');  // Find <a> by text; returns Crawler
$driver->followLink('New User');          // Select and navigate
```

### DOM Querying

```php
$nodes = $driver->filter('table[data-dojo-type="starbug/grid/PagedGrid"] th');
$node  = $driver->filterOne('.alert.alert-danger');  // throws if missing

foreach ($nodes as $node) {
  echo trim($node->textContent);
}
```

`filter()` and `filterOne()` return `Symfony\Component\DomCrawler\Crawler` nodes.

### Downloading

```php
$csv = $driver->download('api/users/admin.csv');
```

### Response Inspection

```php
$driver->getStatusCode();      // 200, 302, etc.
$driver->getResponseBody();     // Full HTML/JSON body
$driver->getUri()->getPath();   // Full root-relative path after redirects (includes base path)
$driver->isOnPath('admin/users'); // True if current path matches (app-relative, no leading slash needed)
$driver->build('admin/users'); // /admin/users (root-relative)
$driver->build('admin/users', true); // http://example.com/admin/users (absolute)
$driver->relativize('https://localhost/blog/admin/users'); // admin/users (app-relative)
$driver->getCookie('sid');      // Cookie from shared jar
$driver->getFormErrors();       // ['field_name' => 'error message', ...]
```

## ShellDriver

Executes CLI commands using Symfony Process with an argv array (safe from shell injection).

```php
$driver = new ShellDriver();

// Capture stdout, stderr, exit code
$result = $driver->run(['php', 'sb', 'query', 'users', '-t']);
echo $result['stdout'];

// Throw on non-zero exit
$output = $driver->runOrFail(['php', 'sb', 'store', 'users', '-t', 'first_name:Dave']);
```

## WebAssertions Trait

Used by `WebTestCase` and `WebAdapter`. Provides PHPUnit assertions that read driver state. The driver itself does not contain assertion methods.

```php
$this->assertPageContains('Welcome');
$this->assertPageDoesNotContain('Error');
$this->assertElementContains('h1', 'Dashboard');
$this->assertElementExists('input[name="email"]');
$this->assertPathEquals('admin/users');     // App-relative, no leading slash needed
$this->assertLinkHrefEquals('New User', 'admin/users/create');
```

### Path assertions: `isOnPath` vs `assertPathEquals`

`isOnPath()` and `assertPathEquals()` both accept app-relative paths (no leading slash) and resolve them through the URI builder before comparing. Use them for routine path checks.

For custom assertions that need the full root-relative path (including the base path), reach through `getUri()->getPath()` directly:

```php
// Routine check — app-relative, base-agnostic
$this->assertTrue($driver->isOnPath('admin/users'));

// Custom assertion — needs the full resolved path
$this->assertSame('/myapp/admin/users', $driver->getUri()->getPath());
```

## Direct Driver Access from Test Cases

It is fine to use the driver directly from a test case for driver-specific checks that don't belong in the domain or adapter:

```php
public function testGridHasColumns(): void {
  $this->getDriver()->get('admin/users');
  $headers = $this->getDriver()->filter('table th');
  // ... custom assertion
}
```

Do not use direct driver access for routine domain flows (login, create user, assert on list page). Those belong in the domain and adapter layers.
