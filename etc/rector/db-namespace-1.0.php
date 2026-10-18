<?php
declare(strict_types=1);

// Migration recipe: db 1.0 namespace moves out of Starbug\Core.
//
// Run in the application root (rector/rector as a dev dependency):
//   vendor/bin/rector process --config vendor/../etc/rector/db-namespace-1.0.php
//
// Rename-only by design: no quality rules, no import normalization beyond
// importing renamed classes, so the diff is limited to classes listed below.
// Paths are supplied on the command line when running.
//
// Note: subclasses that share a short name with the parent (e.g. Save extends
// Save) are left as inline \FQCN extends by Rector's name-collision guard —
// it cannot add a plain use import there without creating a self-reference.

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\String_\RenameStringRector;

return RectorConfig::configure()
    ->withImportNames(true, false, false)
    ->withSkip([
        '*/vendor/*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        'Starbug\Core\Pager' => 'Starbug\Db\Pager',
        'Starbug\Core\Operation\Save' => 'Starbug\Db\Operation\Save',
        'Starbug\Core\Operation\Delete' => 'Starbug\Db\Operation\Delete',
        'Starbug\Core\Operation\SoftDelete' => 'Starbug\Db\Operation\SoftDelete',
    ])
    // Route providers reference operations as raw strings (->onPost("Class\Name")),
    // invisible to class renames.
    ->withConfiguredRule(RenameStringRector::class, [
        'Starbug\Core\Operation\Save' => 'Starbug\Db\Operation\Save',
        'Starbug\Core\Operation\Delete' => 'Starbug\Db\Operation\Delete',
        'Starbug\Core\Operation\SoftDelete' => 'Starbug\Db\Operation\SoftDelete',
    ]);