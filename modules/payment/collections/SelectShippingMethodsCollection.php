<?php
namespace Starbug\Payment;

use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\CollectionFactoryInterface;
use Starbug\Core\SelectCollection;
use Starbug\Db\Schema\SchemerInterface;

class SelectShippingMethodsCollection extends SelectCollection {
  protected $model = "shipping_methods";
  public function __construct(ModelFactoryInterface $models, SchemerInterface $schemer, CollectionFactoryInterface $collections, PriceFormatterInterface $priceFormatter) {
    parent::__construct($models, $schemer);
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
    $products = $this->collections->get("ProductLines")->query(["order" => $this->order]);

    foreach ($rows as &$row) {
      $rates = $this->models->get("shipping_rates")->query()
        ->select("shipping_rates.product_types.slug as product_types")
        ->condition("shipping_rates.shipping_methods_id", $row["id"])
        ->sort("shipping_rates.position")->all();
      foreach ($rates as $idx => $rate) {
        $options = $this->models->get("shipping_rates_product_options")->query()
        ->select("product_options_id.slug")->condition("shipping_rates_product_options.shipping_rates_id", $rate["id"])->all();
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
