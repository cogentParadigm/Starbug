<?php
/**
 * @page config Configuration
 * All configuration files are stored in the folder @em etc/. Let's take a look at the contents of this folder:
 * <ul>
 *   <li>@ref Etc </li>
 *   <li>@ref Host </li>
 *   <li>@ref autoload </li>
 *   <li>@ref constraints </li>
 *   <li>@ref migrationsdir </li>
 * </ul>
 * @section Etc Etc.php
 * @em Etc.php is the project wide configuration file and it holds some basic constants that should apply to the application in ALL host environments.
 * These can be accessed as constants of the @em Etc class like so: @code <?php echo Etc::DEBUG; ?> @endcode
 * @section Host Host.php
 * @em Host.php is the @em host @em specific configuration file. It holds constants that are dependent on the particular host environment the application is running in.
 * These constants can also be accessed through the @em Etc class like so: @code <?php echo Etc::WEBSITE_URL; ?> @endcode
 * @section autoload autoload.php
 * @em autoload.php simply contains an array of utilities to load everytime the application is run.
 * @section constraints constraints.php
 * @em constraints.php contains 2 global arrays. One to hold a list of user groups, and one to hold a list of object statuses.
 * An object is a record in the database. The statuses array defines all of the possible statuses that object can have, such as public, private, or deleted.
 * These groups and statuses are used for access control.
 * @section migrationsdir migrations
 * @em migrations is a folder which contains your applications migrations. Migrations are the starting point in the development cycle of a Starbug application, see @ref migrations .
 */
?>
