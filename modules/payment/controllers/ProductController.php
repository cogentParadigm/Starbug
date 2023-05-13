<?php
namespace Starbug\Payment;

use Starbug\Db\DatabaseInterface;
use Starbug\Routing\Controller;

class ProductController extends Controller {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function __invoke($id) {
    $product = $this->db->get("products", $id);
    $this->assign("product", $product);
    return $this->render("products/details.html");
  }
}
