<?php
declare(strict_types=1);

// Migration recipe: display extraction out of Starbug\Core into starbug/display,
// with the dgrid adapter moving to the dojo module.
//
// Run in the application root (rector/rector as a dev dependency):
//   vendor/bin/rector process --config etc/rector/display-namespace.php
//
// Rename-only by design: no quality rules, no import normalization beyond
// importing renamed classes, so the diff is limited to classes listed below.
// Paths are supplied on the command line when running.
//
// Templates are not covered. Displays are named in Twig
// (helper("displays").render(...)) and the old short names ("SearchForm",
// (model|title) ~ "Form") no longer resolve at all, because DisplayFactory now
// requires a fully qualified class name. Twig call sites need a manual pass.
//
// Route providers that pass a grid or form must also set it explicitly: the
// list, create, update and delete views no longer fall back to a display
// class derived from the model name.

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withImportNames(true, false, false)
    ->withSkip([
        '*/vendor/*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        // framework
        'Starbug\Core\Display' => 'Starbug\Display\Display',
        'Starbug\Core\ItemDisplay' => 'Starbug\Display\ItemDisplay',
        'Starbug\Core\Renderable' => 'Starbug\Display\Renderable',
        'Starbug\Core\Breadcrumbs' => 'Starbug\Display\Breadcrumbs',
        'Starbug\Core\DisplayFactory' => 'Starbug\Display\DisplayFactory',
        'Starbug\Core\DisplayFactoryInterface' => 'Starbug\Display\DisplayFactoryInterface',
        // displays
        'Starbug\Core\FormDisplay' => 'Starbug\Display\FormDisplay',
        'Starbug\Core\TableDisplay' => 'Starbug\Display\TableDisplay',
        'Starbug\Core\ListDisplay' => 'Starbug\Display\ListDisplay',
        'Starbug\Core\CsvDisplay' => 'Starbug\Display\CsvDisplay',
        'Starbug\Core\LayoutDisplay' => 'Starbug\Display\LayoutDisplay',
        'Starbug\Core\FormActions' => 'Starbug\Display\FormActions',
        'Starbug\Core\FormLayout' => 'Starbug\Display\FormLayout',
        'Starbug\Core\SearchForm' => 'Starbug\Display\SearchForm',
        'Starbug\Core\DeleteForm' => 'Starbug\Display\DeleteForm',
        'Starbug\Core\DropdownDisplay' => 'Starbug\Display\DropdownDisplay',
        'Starbug\Core\CheckboxDisplay' => 'Starbug\Display\CheckboxDisplay',
        'Starbug\Core\RadioDisplay' => 'Starbug\Display\RadioDisplay',
        // hooks
        'Starbug\Core\FormHook' => 'Starbug\Display\Hook\FormHook',
        'Starbug\Core\FormHookFactory' => 'Starbug\Display\Hook\FormHookFactory',
        'Starbug\Core\FormHookFactoryInterface' => 'Starbug\Display\Hook\FormHookFactoryInterface',
        'Starbug\Core\FormCheckboxHook' => 'Starbug\Display\Hook\FormCheckboxHook',
        'Starbug\Core\FormFileHook' => 'Starbug\Display\Hook\FormFileHook',
        'Starbug\Core\FormFileSelectHook' => 'Starbug\Display\Hook\FormFileSelectHook',
        'Starbug\Core\FormHiddenHook' => 'Starbug\Display\Hook\FormHiddenHook',
        'Starbug\Core\FormHtmlHook' => 'Starbug\Display\Hook\FormHtmlHook',
        'Starbug\Core\FormInputHook' => 'Starbug\Display\Hook\FormInputHook',
        'Starbug\Core\FormMultipleCategorySelectHook' => 'Starbug\Display\Hook\FormMultipleCategorySelectHook',
        'Starbug\Core\FormMultipleSelectHook' => 'Starbug\Display\Hook\FormMultipleSelectHook',
        'Starbug\Core\FormPasswordHook' => 'Starbug\Display\Hook\FormPasswordHook',
        'Starbug\Core\FormRadioHook' => 'Starbug\Display\Hook\FormRadioHook',
        'Starbug\Core\FormRadioSelectHook' => 'Starbug\Display\Hook\FormRadioSelectHook',
        'Starbug\Core\FormSelectHook' => 'Starbug\Display\Hook\FormSelectHook',
        'Starbug\Core\FormSubmitHook' => 'Starbug\Display\Hook\FormSubmitHook',
        'Starbug\Core\FormTagSelectHook' => 'Starbug\Display\Hook\FormTagSelectHook',
        'Starbug\Core\FormTemplateHook' => 'Starbug\Display\Hook\FormTemplateHook',
        'Starbug\Core\FormTextareaHook' => 'Starbug\Display\Hook\FormTextareaHook',
        'Starbug\Core\FormTextHook' => 'Starbug\Display\Hook\FormTextHook',
        'Starbug\Core\DisplayHook' => 'Starbug\Display\Hook\DisplayHook',
        'Starbug\Core\DisplayDefaultHook' => 'Starbug\Display\Hook\DisplayDefaultHook',
        'Starbug\Core\DisplayLabelHook' => 'Starbug\Display\Hook\DisplayLabelHook',
        // helpers and collections
        'Starbug\Core\DisplaysHelper' => 'Starbug\Display\Helper\DisplaysHelper',
        'Starbug\Core\BreadcrumbsHelper' => 'Starbug\Display\Helper\BreadcrumbsHelper',
        'Starbug\Core\FormCollection' => 'Starbug\Display\Collection\FormCollection',
        'Starbug\Admin\Db\Query\FormCollection' => 'Starbug\Display\Collection\FormCollection',
        // dojo
        'Starbug\Core\GridDisplay' => 'Starbug\Dojo\Display\GridDisplay',
    ]);
