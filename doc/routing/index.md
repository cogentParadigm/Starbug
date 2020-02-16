[< table of contents](../README.md)

The routing system is responsible for intrepreting URLs and translating them into meaningful results. To make a URL do something in Starbug, you can define routes which will map a URL pattern to a controller.

## Routes

The routing process starts with routes, which map URL patterns to controllers. Routes are defined in `etc/di.php`.

```php
<?php
use DI;

return [
  "routes" => DI\add([
    "news" => [ // <------- "news" is the path
      "title" => "News",
      "controller" => "Starbug\App\NewsController"
    ],
    "news/view/{id:[0-9]+}" => [ // <----- "news/view/{id:[0-9]+}" is another path
      "controller" => "Starbug\App\NewsController",
      "action" => "view"
    ]
  ])
];
```

See [FastRoute](https://github.com/nikic/FastRoute) for more details on defining route URL patterns.

### Route parameters

The route payload can contain the following parameters:

| Field | Description |
|-------|-------------|
| controller | The name of controller class to load. |
| action | *Optional*. The method of the controller to call. Defaults to `defaultAction`. |
| template | *Optional*. Templates are kept in the module path `templates`. The template determines what to do with the requested path. By default, this is empty and will consequently defer to the request format to determine what to load. |
| format | *Optional*. By default, the format field is empty. This means that whatever format was requested by the user is the format we use. For example, if the URL is `api/users/admin.json`, then the format is `json`. If no extension is included in the request URL, then `html` is assumed. Most of the time, template and format will be empty and no extension will be provided in the request. This will result in loading the `html` template. The core version of this template is at `core/app/templates/html.php`, but this can be overridden by themes and modules. |
| layout | *Optional*. You can explicitly set the layout by setting the layout field. The default layout is the value of the `type` field. |
| theme | *Optional*. Set an explicit theme for this path. |

## Controllers

The basic principles of a controller are simple:

1. It has access to the `RequestInterface` and `ResponseInterface` so it is a good place to make decisions based on the request and it is a good place to modify the response.
2. It is responsible for setting the content of the response. Typically you will render a view.
3. If you don't specify an action in the route, the next component of the URL will determine which method is used.

Let's take the route below.

```php
<?php
// ...
  'photos' => [
    'title' => 'Photos',
    'controller' => 'Starbug\App\PhotosController'
  ]
// ...
```

Here is the corresponding controller.

```php
<?php
namespace Starbug\App;
use Starbug\Core\Controller;
class PhotosController extends Controller {
  public $routes = [
    'update' => 'update/{id}'
  ];
  public function init() {
    $this->assign("model", "photos");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
}
```
This produces the following paths.

| path | method |
|------|--------|
| photos | defaultAction() |
| photos/create | create() |
| photos/update/{id} | update($id) |
