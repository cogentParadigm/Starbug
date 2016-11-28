There are many components in Starbug such as migrations, models, controllers, displays, and templates that can be selectively merged or overridden by placing files in the right location.

The list of installed modules is an ordered mapping of php namespaces to filesystem directories. It looks something like this.

```php
<?php
return [
	'modules' => [
		'Starbug\Core' => 'core/app',
		'Starbug\Db' => 'modules/db',
		//...more modules...
		'Starbug\Theme' => 'app/themes/starbug-1',
		'Starbug\App' => 'app'
	]
];
```

The namespaces which are lower down the list have the ability to override and extend the namespaces that are higher up the list. The exact behavior depends on what type of object or file we are talking about.

## Classes

When classes such as models, controllers, and displays are retrieved from their respective factories, we look through each namespace starting from Starbug\App and finishing with Starbug\Core. We use the first one that we find and leave it up the loaded class to extend or replace any implementations that may exist in other namespaces.

We have the class `Starbug\Core\UsersForm` at `core/app/displays/UsersForm.php`. This will be loaded when we request a display named `UsersForm`.

If you create `Starbug\App\UsersForm` at `app/displays/UsersForm.php`, it will be loaded instead and you can choose to extend the class the from core, or replace it entirely.

## Hooks

Hooks are the one type of class that doesn't follow the above rule. In the case of hooks, all hooks are loaded individually.

We have the class `Starbug\Core\hook_store_datetime` at `core/app/hooks/store/datetime.php` that gets loaded when we request hooks by the name `store/datetime`.

If you create `Starbug\App\hook_store_datetime` at `app/hooks/store/datetime.php`, an instance of it will be returned in addition to the hook from core (hooks are provided as an array of instances).

## Templates, Views, and Layouts

Views and layouts are really just templates with more specific purposes. When a template is rendered, we look through each module directory starting from `app/templates` and finishing with `core/app/templates`. We use the first one that we find and there is no build in method of loading the loading the 'parent' template which is being overridden.

There is a template at `core/app/templates/html.php` which will be loaded when we try to render a template named `html`.

If we create a file at `app/themes/starbug-1/templates/html.php` it will be loaded instead.

## Template Hooks

There are also template hooks which are combined instead of overridden.

There is a template hook at `core/app/templates/hook/global.head.php` which will be loaded when we render the template hook `head`.

If we create a file at `app/themes/starbug-1/templates/hook/global.head.php` it will be loaded in addition to the core template.

## Configuration

configuration files, usually within the `etc` directory are combined. Think of it as an array merge.

## Common Terminology

For the purpose of discussion, it helps to establish some terminology for these concepts.

| Term | Description |
|------|-------------|
| Cascade | The traversal of the module namespaces or directories and selection of a target class or file. |
| Standard module hierarchy | A description of the general structure of module directories and paths. All modules must employ the standard module hierarchy. |
| Module path | The path of a file within a module such as `models/Users.php` instead of `app/models/Users.php`. |
