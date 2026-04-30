<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/core',
        __DIR__ . '/etc',
        __DIR__ . '/features',
        __DIR__ . '/modules',
        __DIR__ . '/spec',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(false, false, false, true)
    ->withSkip([
      ClassPropertyAssignToConstructorPromotionRector::class
    ])
    ->withImportNames();
