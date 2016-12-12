[< table of contents](../README.md)

This is a list of directories which are part of the standard module hierarchy. That means they can exist or be defined in any module or theme, as well as in `core/app` or `app`.

Ultimately we will be moving to PSR4 naming conventions and it is now possible to place certain classes like controllers anywhere you like. For example, I can create a controller with the name `Starbug\App\AdminPagesController` which will be loaded if I request `AdminPagesController`. If my routes reference my controller by an explicit namespace (recommended), then my controller is not bound to any any specific namespace.

***

`collections`

Collections represent a query against the database. They allow the mechanics of the query to be encapsulated within the collection so that it can be kept out of other classes which consume collections. They do not yet support being loaded from anywhere like controllers or displays but we will move in that direction.

***

`controllers`

Controllers are closely related to routing. A route is translated into a controller and action. A controller action should stick to routing logic like which view to render, what dependent data should be retrieved for the view, should we redirect to another URL. They should leave heavy lifting to other classes. Think 'thin controllers'.

***

`displays`

Displays are a presentational abstraction for rendering a structured list of some sort. A list of fields in a form, or a list of rows in a table. The list is structured because the rows have specific columns, and those columns have their own attributes. For example, a column may have a label or it may be sortable. Fields in a form are similar, they have a name, a label, a type, and other attributes. Similar to controllers, displays can be requested with or without a namespace. If no namespace is provided, each module namespace will be checked. If an explicit namespace is provided (recommended), then that will be used directly.

***

`etc`

Configuration files are kept here. Most importantly `etc/di.php` which is is the main configuration file and serves as the definition files for PHP-DI.

***

`factory`

The only file contained is this directory is models.json which allows you to specify classes which should be injected into models. This will likely go away when operations executed from from submissions are pulled out of models.

***

`helpers`

Helpers are used to provide dependencies to templates (including views and layouts).

***

`hooks`

There are various types of hooks which are supported in Starbug. Hooks are used to define field storage behaviors, provide form controls, and define macro tokens among other things.

***

`layouts`

Layouts are part of theme layer. They are just a special form of template which wraps the page specific content body. You can change the layout for a specific page in a route or controller.

***

`models`

Models represent your data types. For each table in your schema, there is a model.

***

`public`

Naming a directory public makes it exposed directly to browsers so all css and javascript resources within a module are kept somewhere within a directory named public.

***

`script`

The script directory is for functionality to be executed from the command line interface. If you create a class named `Starbug\App\TestMeCommand` at `app/script/test-me.php`. You can run it with the command `php sb test-me`.

***

`templates`

This is the default directory templates are rendered from.

***

`tests`

This directory is meant for tests. You can also put an `etc/di.php` within this directory to be included only when in test mode.

***

`themes`

Themes act as their own module directory and can include many of the directories listed on this page. One exclusion to this is the themes directory. You cannot have nested themes. Another exclusion to this is any file or directory which applies outside the scope of a routed HTTP request. For example, the scripts and factory directories are used from the command line.

***

`views`

Views are templates but there is generally only one view for a page. A controller renders a view and a view may pull in other templates.
