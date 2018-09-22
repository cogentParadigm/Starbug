[< table of contents](../README.md)

An HTTP request contains various components such as a request method, POST data, and a URL (which itself has multiple parts). The action of translating this request into a meaningful payload is referred to as routing.

## Routes

The routing process starts with routes, which map a URL patterns to controllers. Routes are defined in `etc/di.php`.

```php
<?php
return [
  'routes' => DI\add([
    'news' => [ // <------- 'news' is the path
      'title' => 'News',
      'controller' => 'Starbug\App\NewsController'
    ]
  ])
];
```


| Field | Description |
|-------|-------------|
| path  | The path only needs to be a prefix of possible URIs. So a URI with a path of photos can provide access to any subpath of photos/ without it's own more specific entry. |
| controller | The name of controller class to load. |
| action | *Optional*. The method of the controller to call. Normally, this would be determined by the next url component but you can set it explicity. |
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
  public function default_action() {
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
| photos | default_action() |
| photos/create | create() |
| photos/update/{id} | update($id) |
