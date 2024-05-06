<?php
namespace Starbug\ShippingMethods\Admin;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Core\SelectCollection;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Price\FormatterInterface;

class ShippingMethodsSelectCollection extends SelectCollection {
  protected $model = "shipping_methods";
  protected $order;
  public function __construct(
    DatabaseInterface $db,
    SchemerInterface $schemer,
    protected CollectionFactoryInterface $collections,
    protected FormatterInterface $priceFormatter
  ) {
    parent::__construct($db, $schemer);
    $this->collections = $collections;
    $this->priceFormatter = $priceFormatter;
  }
  public function build($query, $ops) {
    $this->order = $ops["order"];
    $query = parent::build($query, $ops);
    $query->select(["name", "description"], "shipping_methods");
    $query->sort("shipping_methods.position");
    return $query;
  }
  public function filterRows($rows) {
    $products = $this->collections->get(ProductLinesCollection::class)->query(["order" => $this->order]);

    foreach ($rows as &$row) {
      $rates = $this->db->query("shipping_rates")
        ->select("shipping_rates.product_types.slug as product_types")
        ->condition("shipping_rates.shipping_methods_id", $row["id"])
        ->sort("shipping_rates.position")->all();
      foreach ($rates as $idx => $rate) {
        $options = $this->db->query("shipping_rates_product_options")
        ->select("product_options_id.slug")
        ->condition("shipping_rates_product_options.shipping_rates_id", $rate["id"])->all();
        foreach ($options as $option) {
          $rates[$idx]["options"][$option["slug"]] = $option;
        }
        $rates[$idx]["product_types"] = explode(",", $rate["product_types"]);
      }
      $price = 0;
      foreach ($products as $product) {
        foreach ($rates as $rate) {
          if (in_array($product["product_type"], $rate["product_types"])) {
            $apply = true;
            if (!empty($rate["options"])) {
              foreach ($rate["options"] as $slug => $option) {
                if ($option["operator"] == "is equal to" && $product["options"][$slug] != $option["value"]) {
                  $apply = false;
                } elseif ($option["operator"] == "is not equal to" && $product["options"][$slug] == $option["value"]) {
                  $apply = false;
                } elseif ($option["operator"] == "is empty" && !empty($product["options"][$slug])) {
                  $apply = false;
                } elseif ($option["operator"] == "is not empty" && empty($product["options"][$slug])) {
                  $apply = false;
                }
              }
            }
            if ($apply) {
              $price += $rate["price"];
              break;
            }
          }
        }
      }
      $row["price"] = $price;
      $row["label"] .= " - ".$this->priceFormatter->format($price);
    }
    return $rows;
  }
}
