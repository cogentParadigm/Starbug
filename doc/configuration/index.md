[< table of contents](../README.md)

# `etc`

Programmer driven configuration is handled through the `etc` module directory. The following files will be recognized in any `etc` directory and combined. You can usually use `var/etc/` to create environment specific configurations.

| path | description |
|------|-------------|
| db | d |
| di.php | This is the main configuration file and is used to list enabled modules, define routes, and various constants such as time zone and site URL. These files are also used directly as definitions for PHP-DI and therefore control how classes are initialized and wired up. You can put environment specific configurations in `var/etc/di.php`. |
| dojo.json | Define AMD module prefixes and build layers for the dojo build system. |
| stylesheets.json | Define stylesheets to load. |
| themes.json | define enabled themes. |


## Settings API

The settings API uses a database table to store user defined settings that can be changed through the administrative interface.
