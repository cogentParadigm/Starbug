<?php
/**
 * @page sbcommand The sb command
 * Starbug's command line scripts are in the @em script/ folder. Use the sb command to run the scripts in this folder. You can add your own scripts to this folder and run them with sb. If you have bash_completion installed, your script can be tab-completed.
 * Let's take a look at some important core scripts:
 * @li @ref setup_script
 * @li @ref migrate
 * @li @ref generate
 * @li @ref test
 * @section setup_script setup
 * The setup script should be used to setup the application after entering configuration details into @ref Host . You can also use this command when setting up an existing application on a new host.
 * @code sb setup @endcode
 * @section migrate migrate
 * The migrate script is used to update the database schema. To update from the current state to the newest available state, run the following:
 * @code sb migrate @endcode
 * @section generate generate
 * The generate script is used to generate code such as migrations, models, CRUD, and even CSS. Here are a few examples:
 * @code
 * sb generate migration UsersMigration
 * sb generate model users
 * sb generate crud users
 * sb generate css
 * @endcode
 * @section test test
 * If you are using git, the test script will check to see what has been modified and check the syntax. It will then run your unit tests. You can force a syntax check of all files with the @em -s flag like so:
 * @code sb test -s @endcode
 */
?>