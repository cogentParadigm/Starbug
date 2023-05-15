<?php
namespace Starbug\Products\Admin\Products;

use Starbug\Wizard\Controller\WizardController;

class AdminProductsController extends WizardController {
  protected $model = "products";
  protected $formTemplate = "admin/products/form.html";
  public function create() {
    return $this->render("admin/products/wizard.html", $this->getViewParams(["title" => "New Product"]));
  }
  public function update($id) {
    return $this->render(
      "admin/products/wizard.html",
      $this->getViewParams(["formParams" => ["id" => $id], "title" => "Update Product"])
    );
  }
}
