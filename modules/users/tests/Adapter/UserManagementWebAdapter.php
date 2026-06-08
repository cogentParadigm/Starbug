<?php
namespace Starbug\Users\Tests\Adapter;

use Starbug\Testing\Adapter\CrudWebAdapter;

/**
 * HTTP-based protocol adapter for user management.
 *
 * Extends the generic CrudWebAdapter with user-specific paths.
 */
class UserManagementWebAdapter extends CrudWebAdapter implements UserManagementAdapterInterface {

  protected string $basePath = 'admin/users';
  protected string $entityLabel = 'User';
  protected string $tableSelector = 'table[data-dojo-type="starbug/grid/PagedGrid"]';
  protected array $expectedColumns = [
    'First Name', 'Last Name', 'Email', 'Last Visit', 'Groups', 'Status', 'Options'
  ];

}
