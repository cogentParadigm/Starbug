<?php
namespace Starbug\Users\Admin;

use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Db\Collection\SelectCollection;
use Starbug\Db\Operation\Save;
use Starbug\Db\Operation\SoftDelete;
use Starbug\Routing\Route;
use Starbug\Users\Admin\Collection\AdminUsersCollection;
use Starbug\Users\Admin\Controller\ApiUsersController;
use Starbug\Users\Admin\Display\UsersForm;
use Starbug\Users\Admin\Display\UsersGrid;
use Starbug\Users\Admin\Display\UsersSearchForm;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $users = $this->addCrudRoutes($routes->getRoute("admin")->addRoute("/users"), "users");
    $users->getRoute("/delete/{id:[0-9]+}")->onPost(SoftDelete::class);
    $users->setOptions([
      "grid" => UsersGrid::class,
      "form" => UsersForm::class,
      "searchForm" => UsersSearchForm::class
    ]);

    $api = $routes->getRoute("api");
    $this->addApiRoute($api->addRoute("/groups/select.json"), "groups", SelectCollection::class);

    $api->addRoute("/users/admin.{format:csv|json}", ApiUsersController::class, [
      "collection" => AdminUsersCollection::class,
      "model" => "users"
    ])
    ->onPost(Save::class)
    ->onDelete(SoftDelete::class);

    $api->addRoute("/users/select.json", ApiUsersController::class, ["collection" => SelectCollection::class]);
  }
}
