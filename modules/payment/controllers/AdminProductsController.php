<?php
namespace Starbug\Payment;

use Starbug\Core\WizardController;
use Starbug\Core\DatabaseInterface;

class AdminProductsController extends WizardController {
  protected $model = "products";
  protected $formTemplate = "admin/products/form.html";
  public function init() {
    $this->assign("model", "products");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    return $this->render("admin/products/wizard.html", ["options" => $this->getDisplayOptions(), "title" => "New Product"]);
  }
  public function update($id) {
    $this->assign("id", $id);
    return $this->render("admin/products/wizard.html", ["options" => $this->getDisplayOptions(["id" => $id]), "title" => "Update Product"]);
  }
}
