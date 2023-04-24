<?php
namespace Starbug\Tachyons;

use Starbug\Core\Controller;
use Starbug\Js\DojoConfiguration;

class StyleguideController extends Controller {
  protected $colors = [
    "Colors" => [
      "#494" => "brand-green"
    ],
    "Grayscale" => [
      "#000" => "black",
      "#111" => "near-black",
      "#333" => "dark-gray",
      "#555" => "mid-gray",
      "#777" => "gray",
      "#999" => "silver",
      "#AAA" => "light-silver",
      "#CCC" => "moon-gray",
      "#EEE" => "light-gray",
      "#F4F4F4" => "near-white",
      "#FFF" => "white"
    ],
    "Translucent" => [
      "rgba(0,0,0,.9)" => "black-90",
      "rgba(0,0,0,.8)" => "black-80",
      "rgba(0,0,0,.7)" => "black-70",
      "rgba(0,0,0,.6)" => "black-60",
      "rgba(0,0,0,.5)" => "black-50",
      "rgba(0,0,0,.4)" => "black-40",
      "rgba(0,0,0,.3)" => "black-30",
      "rgba(0,0,0,.2)" => "black-20",
      "rgba(0,0,0,.1)" => "black-10",
      "rgba(0,0,0,.05)" => "black-05",
      "rgba(255,255,255,.9)" => "white-90",
      "rgba(255,255,255,.8)" => "white-80",
      "rgba(255,255,255,.7)" => "white-70",
      "rgba(255,255,255,.6)" => "white-60",
      "rgba(255,255,255,.5)" => "white-50",
      "rgba(255,255,255,.4)" => "white-40",
      "rgba(255,255,255,.3)" => "white-30",
      "rgba(255,255,255,.2)" => "white-20",
      "rgba(255,255,255,.1)" => "white-10"
    ]
  ];
  public function __construct(DojoConfiguration $dojo) {
    $this->dojo = $dojo;
  }
  public function defaultAction() {
    return $this->redirect("styleguide/tachyons/colors");
  }
  public function colors() {
    return $this->render("styleguide/template.html", ["page" => "colors", "sections" => $this->colors]);
  }
  public function type() {
    return $this->render("styleguide/template.html", ["page" => "type"]);
  }
  public function scales() {
    return $this->render("styleguide/template.html", ["page" => "scales"]);
  }
  public function content() {
    return $this->render("styleguide/template.html", ["page" => "content"]);
  }
  public function controls() {
    return $this->render("styleguide/template.html", ["page" => "controls"]);
  }
  public function dgrid() {
    $this->dojo->addDependency("starbug/grid/PagedGrid");
    $this->dojo->addDependency("starbug/grid/columns/options");
    return $this->render("styleguide/template.html", ["page" => "dgrid"]);
  }
}
