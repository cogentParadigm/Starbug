<?php
declare(strict_types=1);

// Migration recipe: the state package is retired in favour of PSR-16.
//
// Run in the application root (rector/rector as a dev dependency):
//   vendor/bin/rector process --config etc/rector/state-namespace.php
//
// Rename-only by design: no quality rules, no import normalization beyond
// importing renamed classes, so the diff is limited to classes listed below.
// Paths are supplied on the command line when running.
//
// This one is not purely mechanical. PSR-16 has no resetCache(); clear()
// deletes every stored value rather than only the in-memory copy, and
// getMultiple() takes a default value. Renaming leaves get/set/delete calls
// working, but review any resetCache() call site.

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withImportNames(true, false, false)
    ->withSkip([
        '*/vendor/*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        'Starbug\State\StateInterface' => 'Psr\SimpleCache\CacheInterface',
        'Starbug\State\DatabaseState' => 'Starbug\Db\SimpleCache\DatabaseCache',
        'Starbug\State\Migration\State' => 'Starbug\Db\Migration\State',
    ]);
