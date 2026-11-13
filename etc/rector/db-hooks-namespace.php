<?php
declare(strict_types=1);

// Migration recipe: the store hooks that need auth, users or files have moved
// into those modules. The hooks whose dependencies must not point back at db
// (operation, slug) remain in Starbug\Db\Query\Hook.
//
// Run in the application root (rector/rector as a dev dependency):
//   vendor/bin/rector process --config etc/rector/db-hooks-namespace.php
//
// Rename-only by design: no quality rules, no import normalization beyond
// importing renamed classes, so the diff is limited to classes listed below.
// Paths are supplied on the command line when running.
//
// The hook registry keys ("owner", "password", "upload") are unchanged, so
// column options and the form query executor need no edits.

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\String_\RenameStringRector;

return RectorConfig::configure()
    ->withImportNames(true, false, false)
    ->withSkip([
        '*/vendor/*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        'Starbug\Db\Query\Hook\StoreOwnerHook' => 'Starbug\Users\Query\Hook\StoreOwnerHook',
        'Starbug\Db\Query\Hook\StorePasswordHook' => 'Starbug\Users\Query\Hook\StorePasswordHook',
        'Starbug\Db\Query\Hook\StoreUploadHook' => 'Starbug\Files\Query\Hook\StoreUploadHook',
    ])
    // Dependency injection configuration names hooks as strings.
    ->withConfiguredRule(RenameStringRector::class, [
        'Starbug\Db\Query\Hook\StoreOwnerHook' => 'Starbug\Users\Query\Hook\StoreOwnerHook',
        'Starbug\Db\Query\Hook\StorePasswordHook' => 'Starbug\Users\Query\Hook\StorePasswordHook',
        'Starbug\Db\Query\Hook\StoreUploadHook' => 'Starbug\Files\Query\Hook\StoreUploadHook',
    ]);
