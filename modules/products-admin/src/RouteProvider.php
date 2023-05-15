<?php
namespace Starbug\Products\Admin;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;
use Starbug\Payment\Product\Save;
use Starbug\Products\Admin\Categories\ProductCategoriesAdminCollection;
use Starbug\Products\Admin\Categories\ProductCategoriesForm;
use Starbug\Products\Admin\Categories\ProductCategoriesGrid;
use Starbug\Products\Admin\ProductOptions\AdminCollection as ProductOptionsAdminCollection;
use Starbug\Products\Admin\ProductOptions\ProductOptionsForm;
use Starbug\Products\Admin\ProductOptions\ProductOptionsGrid;
use Starbug\Products\Admin\Products\AdminProductsController;
use Starbug\Products\Admin\Products\ProductsForm;
use Starbug\Products\Admin\Products\ProductsGrid;
use Starbug\Products\Admin\ProductTypes\ProductTypesForm;
use Starbug\Products\Admin\ProductTypes\ProductTypesGrid;
use Starbug\Routing\Controller\ViewController;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {

    // Categories
    $categories = $this->addCrud($routes, "product_categories", [
      "grid" => ProductCategoriesGrid::class,
      "form" => ProductCategoriesForm::class
    ]);
    $categories["adminApi"]->setOption("collection", ProductCategoriesAdminCollection::class);

    // Options
    $productOptions = $this->addCrud($routes, "product_options", [
      "grid" => ProductOptionsGrid::class,
      "form" => ProductOptionsForm::class
    ]);
    $productOptions["adminApi"]->setOption("collection", ProductOptionsAdminCollection::class);

    // Products
    $products = $this->addCrud($routes, "products", [
      "grid" => ProductsGrid::class,
      "form" => ProductsForm::class
    ]);
    $products["create"]->setController([AdminProductsController::class, "create"])
      ->onPost(Save::class);
    $products["update"]->setController([AdminProductsController::class, "update"])
      ->onPost(Save::class);
    $products["list"]->addRoute("/form.{format:xhr}")
      ->setController([AdminProductsController::class, "form"]);

    // Types
    $productTypes = $this->addCrud($routes, "product_types", [
      "grid" => ProductTypesGrid::class,
      "form" => ProductTypesForm::class
    ]);
    $productTypes["update"]
      ->setController(ViewController::class)
      ->setOptions([
        "view" => "admin/product-types/update.html",
        "product_options_grid" => ProductOptionsGrid::class
      ]);
  }
}
