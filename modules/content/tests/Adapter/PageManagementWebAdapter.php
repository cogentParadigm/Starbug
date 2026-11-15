<?php
namespace Starbug\Content\Tests\Adapter;

use Starbug\Admin\Testing\Adapter\CrudWebAdapter;

/**
 * HTTP-based protocol adapter for page management.
 *
 * Extends the generic CrudWebAdapter with the content module's paths.
 */
class PageManagementWebAdapter extends CrudWebAdapter {

  protected string $basePath = 'admin/pages';
  protected string $entityLabel = 'Page';
  protected string $entity = 'pages';
  protected string $tableSelector = 'table[data-dojo-type="starbug/grid/PagedGrid"]';
  protected array $listColumns = [
    'Title', 'Published', 'Last Modified', 'Options'
  ];
}
