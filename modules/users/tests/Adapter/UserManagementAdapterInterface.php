<?php
namespace Starbug\Users\Tests\Adapter;

use Starbug\Testing\Adapter\CrudAdapterInterface;

/**
 * Marker interface for user management CRUD adapters.
 *
 * Extends CrudAdapterInterface so the domain layer can type-hint
 * this interface while still benefiting from the generic CRUD base.
 */
interface UserManagementAdapterInterface extends CrudAdapterInterface {
}
