# Skill Maintenance Notes

This file tracks future implementation work that will require updating the `testing` skill. If you implement any of these items, update the relevant skill documents.

## Pending Implementations

### Shell test container auto-wiring
**Current state:** Shell tests require manual wiring (`new ShellDriver()`, `new ShellAdapter($driver)`, `new Domain($adapter)`).
**What changes:** When shell tests support `#[Inject]` wiring like web tests do, remove the manual wiring note from `SKILL.md`.
**Doc to update:** `SKILL.md` (Shell acceptance tests section)

### Browser / Panther support
**Current state:** No JS browser driver exists. The `DirectDriver` covers all current acceptance tests.
**What changes:** If a `BrowserDriver` wrapping Symfony Panther is added, update the skill to document:
- New base class or driver factory option in `WebTestCase`
- Panther-specific methods (screenshot on failure, executeScript, wait helpers)
- Browser adapter example in `four-layer-architecture.md`
- When to use `DirectDriver` vs `BrowserDriver`
**Docs to update:** `SKILL.md`, `references/driver-capabilities.md`, `references/four-layer-architecture.md`

### Deferred browser-only capabilities
These require a browser driver and are explicitly deferred until a concrete JS scenario is needed. Do not write in-process tests that attempt these:

- Dialog wait/close assertions
- Wizard step progression
- Autocomplete widget interaction
- Grid wait (`waitForGrid`) for async Dojo widgets
- File upload through browser file input
- Screenshot on failure
- JavaScript execution (`executeScript()`)

**Docs to update:** `references/driver-capabilities.md` (add "Deferred / Browser-Only" section)

### New DomCrawler convenience methods
**Current state:** `filter()`/`filterOne()` return `Crawler` nodes. Element property extraction (text, attr, html, hasClass) is done directly on the Crawler.
**What changes:** If dedicated convenience methods are added to `WebDriverInterface` or `WebAssertions`, document them in the driver reference.
**Doc to update:** `references/driver-capabilities.md`

### Domain-specific assertion helpers
**Current state:** Table assertions, CSV comparisons, and other reusable DSL patterns are project-specific and live in adapters.
**What changes:** If reusable helpers (e.g. `assertPagedGridHasColumns`, `assertCsvContains`) are promoted to `starbug/testing`, update the layer guidance to show them as domain or adapter patterns.
**Doc to update:** `references/four-layer-architecture.md`

## Completed

### URL handling simplification
**Status:** Completed. `AbstractWebDriver` and `DirectDriver` now use `UriBuilderInterface` directly.
- `getBaseUrl()`, `getBasePath()`, and `getAppRelativePath()` removed from `WebDriverInterface`.
- Added `build($path, $absolute)` and `relativize($path)` to `WebDriverInterface`.
- `getCurrentPath()` now returns the root-relative path (including basePath if present).
- `UriBuilderInterface::relativize()` added to `starbug/http` interface.
- DI wiring in `testing/etc/di.php` simplified: no more `baseUrl`/`basePath` constructor parameters.
**Doc updated:** `references/driver-capabilities.md`

## Cleanup Reminders

- **Behat artifacts:** When Behat is fully removed (Track 6), verify no stale references remain in the skill.
- **Fixture attribute evolution:** If `#[Fixture()]` gains new options (e.g. non-YAML types), update the fixtures section in `SKILL.md`.
