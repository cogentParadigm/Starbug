<?php
namespace Starbug\Users\Tests\Adapter;

use Starbug\Db\Testing\Adapter\CrudShellAdapter;

/**
 * Shell-based protocol adapter for user management.
 *
 * Extends the generic CrudShellAdapter with the users entity.
 */
class UserManagementShellAdapter extends CrudShellAdapter implements UserManagementAdapterInterface {

  protected string $entity = 'users';

}
