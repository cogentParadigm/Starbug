<?php
declare(strict_types=1);

// Migration recipe: import pipeline extraction out of the imports module into
// starbug/importer, starbug/importer-spreadsheet and starbug/db.
//
// Run in the application root (rector/rector as a dev dependency):
//   vendor/bin/rector process --config etc/rector/imports-namespace.php
//
// Rename-only by design: no quality rules, no import normalization beyond
// importing renamed classes, so the diff is limited to classes listed below.
// Paths are supplied on the command line when running.
//
// Classes that stayed in the imports module are deliberately absent:
// Read\CollectionStrategy, Read\StrategyFactory, Transform\Lookup,
// OperationsRepository, ImporterFactory and the Operation classes.

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withImportNames(true, false, false)
    ->withSkip([
        '*/vendor/*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        // pipeline
        'Starbug\Imports\Import' => 'Starbug\Importer\Import',
        'Starbug\Imports\Importer' => 'Starbug\Importer\Importer',
        'Starbug\Imports\Read\Strategy' => 'Starbug\Importer\Read\Strategy',
        'Starbug\Imports\Read\StrategyInterface' => 'Starbug\Importer\Read\StrategyInterface',
        'Starbug\Imports\Read\FileStrategy' => 'Starbug\Importer\Read\FileStrategy',
        'Starbug\Imports\Read\StrategyFactoryInterface' => 'Starbug\Importer\Read\StrategyFactoryInterface',
        'Starbug\Imports\Read\Traits\Transformers' => 'Starbug\Importer\Read\Traits\Transformers',
        'Starbug\Imports\Read\YamlStrategy' => 'Starbug\Importer\Read\YamlStrategy',
        'Starbug\Imports\Write\StrategyInterface' => 'Starbug\Importer\Write\StrategyInterface',
        'Starbug\Imports\Write\StrategyFactory' => 'Starbug\Importer\Write\StrategyFactory',
        'Starbug\Imports\Write\StrategyFactoryInterface' => 'Starbug\Importer\Write\StrategyFactoryInterface',
        'Starbug\Imports\Transform\AbstractTransformer' => 'Starbug\Importer\Transform\AbstractTransformer',
        'Starbug\Imports\Transform\Constant' => 'Starbug\Importer\Transform\Constant',
        'Starbug\Imports\Transform\Factory' => 'Starbug\Importer\Transform\Factory',
        'Starbug\Imports\Transform\TransformerInterface' => 'Starbug\Importer\Transform\TransformerInterface',
        // spreadsheet strategies
        'Starbug\Imports\Read\TabularSpreadsheetStrategy' => 'Starbug\Importer\Spreadsheet\Read\TabularSpreadsheetStrategy',
        'Starbug\Imports\Read\WorksheetNotFoundException' => 'Starbug\Importer\Spreadsheet\Read\WorksheetNotFoundException',
        'Starbug\Imports\Read\PhpSpreadsheet\IReadFilter\HeaderRowFilter' => 'Starbug\Importer\Spreadsheet\Read\PhpSpreadsheet\IReadFilter\HeaderRowFilter',
        'Starbug\Imports\Script\SpreadsheetInfo' => 'Starbug\Importer\Spreadsheet\Script\SpreadsheetInfo',
        // database strategies
        'Starbug\Imports\Read\YamlFixtureStrategy' => 'Starbug\Db\Importer\Read\YamlFixtureStrategy',
        'Starbug\Imports\Write\DatabaseStrategy' => 'Starbug\Db\Importer\Write\DatabaseStrategy',
        'Starbug\Imports\Write\FixtureStrategy' => 'Starbug\Db\Importer\Write\FixtureStrategy',
    ]);
