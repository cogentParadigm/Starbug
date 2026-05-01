# Upgrading from PHP 7.4 to PHP 8.2

This is a significant upgrade. It involves adding new core prerequisites from a newer starbug version, updating dependencies, updating infrastructure configs, running a PHPCompatibility scan, running rector, updating PHPUnit configuration, and addressing runtime issues that surface during testing.

## Directions

### 0. Run tests before the upgrade

Before making any changes, run the existing test suite and record the test and pass counts. This establishes a baseline and helps detect tests that silently stop running during the upgrade.

Use the project's Jenkinsfile (located via the `team-conventions` skill) to identify which tests and test suites should be run. Typical commands:

```bash
# PHPUnit
ddev exec vendor/bin/phpunit -c etc/phpunit.xml

# Behat
ddev exec vendor/bin/behat
```

Record the total test count and pass count. Compare against these numbers after the upgrade is complete. If the count drops, some tests may have been skipped or broken.

### 1. Check prerequisites in the current project

Before upgrading, check that the project has the newer core files that updated packages expect. These were added in newer starbug but may not exist in older projects. If any are missing, ask the user where to source them from (e.g. a newer starbug checkout, a specific tag, or another project they consider compatible) and copy them in:

- `core/src/Operation/Delete.php`
- `core/src/Operation/Save.php`
- `core/src/Operation/SoftDelete.php`
- `modules/db/src/Operation/Migrate.php`
- `modules/db/src/Query/Traits/Metadata.php` (only for newer `modules/db/src/Query/` structure; skip for legacy `modules/db/classes/query.php`)
- `modules/imports/` (entire module directory)
- `modules/event-dispatcher/` (entire module directory)

> **Note:** Only copy these if they are absent. Do not overwrite project-specific modifications.

#### Query implementation compatibility

Check which database Query implementation the project uses. This determines which set of diffs to apply in Step 12.

- **Newer structure:** `modules/db/src/Query/Query.php` — copy the `Metadata.php` trait and apply the diffs in `reference-post-upgrade-fixes.diff`.
- **Legacy structure:** `modules/db/classes/query.php` — skip the `Metadata.php` trait and apply the diffs in `reference-legacy-query.diff` instead. The hook fixes are simpler because the legacy query class exposes `public $fields` directly.

#### Enable new modules

For projects that do not auto-discover modules via composer, you must also enable the new modules in the project's root `etc/di.php`. See `reference-enable-modules.diff` for the expected changes to the `modules` array.

> If the project already registers these modules via a composer-based discovery mechanism, this step can be skipped.

### 2. Update `composer.json`

Apply the changes shown in `reference-composer.diff`. Key changes:

- Remove `config.platform.php` (was likely pinned to `7.1.3` or similar)
- Remove `php-console/php-console` (deprecated/incompatible)
- Add new dependencies:
  - `symfony/event-dispatcher: ^7.2`
  - `starbug/operation: ^0.8`
- Add/update dev dependencies:
  - `phpunit/phpunit: ^9.0` (lock to v9, do not go past 9.x)
  - `rector/rector: ^1.0.4`

> **Note:** The diff also updates `solarium/solarium` from `^5.1` to `^6.3`. Only apply this if your project uses Solr. Skip it otherwise.

### 3. Update infrastructure configs

Apply the changes shown in `reference-infrastructure.diff`:

- `.ddev/config.yaml`: change `php_version` from `7.4` to `8.2`, update `webimage_extra_packages` if needed (e.g. `php8.2-yaml`)
- `docker-compose.yml`: update PHP image tag if applicable
- `behat.yml`: update context class references if namespaces changed

### 4. Run `composer update`

```bash
ddev exec composer update -W
```

You may need to run this **twice** — old plugins may error the first time and resolve on the second pass.

> **Note:** You will probably need to delete `modules/webpack` and `modules/dojo` so that composer can re-install them when you update.

### 5. Restart DDEV for the new PHP version

```bash
ddev restart
```

### 6. Run PHPCompatibility scan

Install PHPCompatibility temporarily, scan the codebase, then remove it:

```bash
ddev exec composer require --dev "phpcompatibility/phpcompatibility:dev-develop as 9.6.x-dev"
ddev exec vendor/bin/phpcs --extensions=php --standard=PHPCompatibility core app modules
ddev exec composer remove --dev phpcompatibility/phpcompatibility
```

Review the scan output. It will flag PHP 8.2 incompatibilities that rector may not cover. Address any critical issues before proceeding.

### 7. Create rector config

Create `rector.php` in the project root. Use the provided `rector.php` from this directory as a template. Adjust the `withPaths()` array if your project structure differs.

### 8. Run rector

```bash
ddev exec vendor/bin/rector process
```

Rector will apply mechanical PHP 8.0+ changes across the codebase:
- `get_class($x)` → `$x::class`
- Large `switch` statements → `match()`
- `isset($x) ? $y : $z` → `$x ?? $z`
- Trait use statements: `use Traits\Foo;` → `use Foo;`
- DI container strings: `DI\object('Foo')` → `object(Foo::class)`

Review the rector output. You may need to run it a second time or apply manual fixes where rector could not safely transform code.

### 9. Update PHPUnit configuration

Migrate `etc/phpunit.xml` (or wherever your config lives) from PHPUnit 8 to 9 format. See `reference-phpunit-config.diff` for the expected changes:

- Add `xmlns:xsi` and `xsi:noNamespaceSchemaLocation`
- Replace `<filter><whitelist>` with `<coverage><include>`
- Replace `<log type="...">` with `<junit outputFile="...">`
- Move coverage clover inside `<coverage><report>`

### 10. Update the test fixture system (if using legacy DbUnit)

If the project uses the old PHPUnit DbUnit-based `DatabaseTestCase` (common in older starbug projects), it will not run under PHP 8. Rewrite it to use the YAML fixture import system from the newer starbug core before attempting to run tests with PHP 8:

- Replace `PHPUnit\DbUnit\TestCaseTrait`, `PDO`, `Composite`, and `Factory` with `Starbug\Imports\Importer`, `Starbug\Imports\Read\YamlFixtureStrategy`, `Starbug\Imports\Write\FixtureStrategy`, and `Starbug\Db\Operation\Migrate`.
- Replace `getSetUpOperation()` / `getConnection()` / `getDataSet()` with `setUp()`, `getImporter()`, `getDataSets()`, and `createYamlDataSet()`.
- Convert XML fixture files to YAML.
- See `reference-post-upgrade-fixes.diff` for a concrete example.

### 11. Update cron scripts and development tooling

- Search `app/bin/cron/` (or wherever cron jobs live) for hardcoded PHP binary paths like `ea-php71` and update them to the target PHP version (e.g. `ea-php82`).
- Update `.vscode/launch.json` XDebug port from `9000` to `9003` if using Xdebug 3.
- If the jenkinsfile uses legacy custom docker-compose environment, load the ddev skill to help upgrade it to ddev

### 12. Run tests and fix runtime issues iteratively

This is a critical step. Rector handles mechanical syntax but many runtime issues only surface during execution. After running PHPUnit or Behat, apply fixes using the patterns in `reference-post-upgrade-fixes.diff`.

Common post-upgrade issues:

| Issue | Example fix |
|-------|-------------|
| Undefined array key in conditions | `if ($ops["key"])` → `if (!empty($ops["key"]))` |
| Undefined array key in assignments | `$named["reset"]` → `$named["reset"] ?? false` |
| Ternary on null (`?` / `?:`) | `$options["sort"] ?: ""` → `$options["sort"] ?? ""` |
| Uninitialized variable | Add `$isPdf = false;` before conditional use |
| Appending to undefined array key | Initialize with `$arr["key"] = [];` first |
| Nullable object properties | `$result->Foo__c` → `$result->Foo__c ?? ""` |
| Uninitialized dynamic properties | Add `protected $prop = null;` to class |
| Missing default for loop/array access | `$ops += ["direction" => "ASC"];` before use |

#### Starbug core patterns that often need manual fixes

| Issue | Example fix |
|-------|-------------|
| `create_function()` removed in PHP 8 | Replace with real anonymous function. Only applies to newer structure (`modules/db/src/Query/Executor.php`). |
| `E_STRICT` deprecated | Remove `E_STRICT` from custom error handler level maps and `getErrorName()` switch statements. |
| `fgetcsv`/`fputcsv` default escape changed | Pass `escape: ""` explicitly: `fgetcsv($handle, escape: "")`. |
| `StoreOrderedHook` dynamic properties | **Newer path:** Rewrite to query metadata (`$query->setMeta()` / `$query->getMeta()` / `$query->removeMeta()`). See `reference-post-upgrade-fixes.diff`. **Legacy path:** No rewrite needed if properties are already declared; otherwise add `protected $conditions = false; protected $value = false; protected $increment = 1;`. See `reference-legacy-query.diff`. |
| `StoreUniqueHook` / `StoreSlugHook` field access | **Newer path:** Replace direct `$query->fields[$c]` with `$query->hasValue($c)` / `$query->getValue($c)` guards. See `reference-post-upgrade-fixes.diff`. **Legacy path:** Guard with `isset($query->fields[$c])`. See `reference-legacy-query.diff`. |
| `Table::get()` entity lookup null safety | Initialize `$conditions = false;` and add empty/isset guards before returning. |
| `FormDisplay::get()` array access | Add `?? null` fallback: `$var = $var[rtrim($p, "]")] ?? null;`. |
| `GridDisplay` option guards | Wrap `$options['attributes']` and `$options['dnd']` with `!empty()`. |
| Test assertion strict types | Change `assertSame("1", ...)` → `assertSame(1, ...)` when PHP 8 returns strict ints. |

Run tests repeatedly, fixing each error as it appears, until the test suite passes.

Compare the final test and pass counts against the baseline recorded in Step 0. If counts differ, investigate whether tests were skipped, removed, or broken.

## Notes

- This upgrade path was extracted from a real production upgrade (ECS-602). Project-specific code may have additional issues not covered here. The key is to test iteratively.
