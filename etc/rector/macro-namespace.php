<?php
declare(strict_types=1);

// Migration recipe: macro extraction out of Starbug\Core into starbug/macro.
//
// Run in the application root (rector/rector as a dev dependency):
//   vendor/bin/rector process --config vendor/../etc/rector/macro-namespace.php
//
// Rename-only by design: no quality rules, no import normalization beyond
// importing renamed classes, so the diff is limited to classes listed below.
// Paths are supplied on the command line when running.

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withImportNames(true, false, false)
    ->withSkip([
        '*/vendor/*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        'Starbug\Core\Macro' => 'Starbug\Macro\Macro',
        'Starbug\Core\MacroInterface' => 'Starbug\Macro\MacroInterface',
        'Starbug\Core\MacroHook' => 'Starbug\Macro\MacroHook',
        'Starbug\Core\MacroHookFactory' => 'Starbug\Macro\MacroHookFactory',
        'Starbug\Core\MacroHookFactoryInterface' => 'Starbug\Macro\MacroHookFactoryInterface',
    ]);