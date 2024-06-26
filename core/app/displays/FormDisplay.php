<?php
namespace Starbug\Core;

use Starbug\Db\CollectionFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Bundle\Bundle;
use Starbug\Templates\TemplateInterface;

class FormDisplay extends ItemDisplay {
  public $type = "form";
  public $template = "form.html";
  public $collection = "Form";
  public $input_name = false;

  public $url;
  public $method = "post";
  public $errors;
  public $layout;
  public $defaultAction = "create";
  public $submit_label = "Save";
  public $cancelable = true;
  public $cancel_url = "";
  public $actions;
  protected $vars = [];
  public $horizontal = false;
  protected $layoutDisplay = FormLayout::class;
  protected $actionsDisplay = FormActions::class;
  protected $showSuccessMessage = true;
  protected $isXhr = false;
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    protected FormHookFactoryInterface $hookFactory,
    protected DisplayFactoryInterface $displays,
    protected ServerRequestInterface $request
  ) {
    parent::__construct($output, $collections);
    $this->errors = $this->request->getAttribute("state") ?? new Bundle();
    $route = $this->request->getAttribute("route");
    if ($route->hasOption("cancelUrl") && empty($this->cancel_url)) {
      $this->cancel_url = $route->getOption("cancelUrl");
    }
    $this->isXhr = $route->getOption("format") == "xhr";
  }

  public function build($options = []) {
    $this->options = $options;
    if (empty($this->model) && !empty($this->options['model'])) {
      $this->model = $this->options['model'];
    }
    if (!empty($options["input_name"])) {
      $this->input_name = $options["input_name"];
    }
    if (false === $this->input_name) {
      $this->input_name = [];
    }

    // create layout display
    $this->layout = $this->displays->get($this->layoutDisplay);
    $this->layout->build(["modal" => $this->isXhr]);
    // create actions display
    $this->actions = $this->displays->get($this->actionsDisplay);
    $this->actions->build([
      "modal" => $this->isXhr,
      "defaultAction" => $this->defaultAction,
      "defaultActionLabel" => $this->submit_label
    ]);

    // run query
    $this->beforeQuery($options);
    $this->query();
    $this->buildDisplay($options);
  }

  /**
   * This method is called before the query method.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  protected function beforeQuery($options) {
    // override this method if needed
  }

  /**
   * Filter columns to set the input type and some other defaults.
   */
  public function filter($field, $options) {
    if (empty($options["input_type"])) {
      $options["input_type"] = "text";
    }
    return $options;
  }

  /**
   * Override query function to only query with id.
   */
  public function query($options = null) {
    // set options
    if (is_null($options)) {
      $options = $this->options;
    }

    if (empty($options['id'])) {
      $this->items = [];
    } else {
      parent::query(["action" => $this->defaultAction] + $options);
    }

    $queryParams = $this->request->getQueryParams();

    if (!empty($queryParams["copy"]) && is_numeric($queryParams["copy"]) && empty($this->items)) {
      $options['id'] = $queryParams["copy"];
      parent::query(["action" => $this->defaultAction] + $options);
      if (!empty($this->items)) {
        unset($this->items[0]['id']);
      }
    }

    // load POST data
    if (!empty($this->items)) {
      if (!$this->hasPost()) {
        $this->setPost([]);
      }
      foreach ($this->items[0] as $k => $v) {
        if (!$this->hasPost($k)) {
          $this->setPost($k, $v);
        }
      }
    }
  }

  protected function beforeRender() {
    // set form attributes
    $this->attributes["action"] = $this->url;
    $this->attributes["method"] = $this->method;
    $this->attributes["accept-charset"] = "UTF-8";
    if (!empty($this->model) && !empty($this->defaultAction)) {
      if ($this->success()) {
        $this->attributes['class'][] = "submitted";
      } elseif ($this->failure()) {
        $this->attributes['class'][] = "errors";
      }
    }
  }

  public function render($query = false) {
    parent::render($query);
  }

  public function errors($key = "", $values = false) {
    if (is_bool($key)) {
      $values = $key;
      $key = "";
    }
    $parts = empty($key) ? [] : explode("[", $key);
    $parts = array_map(function ($part) {
      return rtrim($part, "]");
    }, $parts);
    if ($values) {
      return $this->errors->get(...$parts);
    } elseif (!empty($parts)) {
      return $this->errors->has(...$parts);
    } else {
      return !$this->errors->isEmpty();
    }
  }

  public function success() {
    return $this->request->getAttribute("state") && $this->errors->isEmpty();
  }

  public function failure() {
    return $this->request->getAttribute("state") && !$this->errors->isEmpty();
  }

  public function showSuccessMessage() {
    return $this->showSuccessMessage
      && !empty($this->model)
      && !empty($this->defaultAction)
      && $this->success();
  }

  public function hasPost(...$keys) {
    $keys = array_merge($this->input_name, $keys);
    $target = $this->request->getParsedBody();
    while (!empty($keys)) {
      $key = array_shift($keys);
      if (is_array($target) && array_key_exists($key, $target)) {
        $target = $target[$key];
      } else {
        return false;
      }
    }
    return !empty($target);
  }

  public function getPost(...$keys) {
    $keys = array_merge($this->input_name, $keys);
    $value = $this->request->getParsedBody();
    foreach ($keys as $key) {
      if (!isset($value[$key])) {
        return null;
      }
      $value = $value[$key];
    }
    return $value;
  }

  public function setPost(...$keys) {
    $keys = array_merge($this->input_name, $keys);
    $data = $this->request->getParsedBody();
    $value = array_pop($keys);
    $target = &$data;
    foreach ($keys as $key) {
      if (!is_array($target)) {
        $target = [];
      }
      $target = &$target[$key];
    }
    $target = $value;
    $this->request = $this->request->withParsedBody($data);
  }

  /**
   * Get the full name attribute
   * eg. name becomes users[name]
   * eg. name[] becomes users[name][]
   *
   * @param string $name the relative name
   */
  public function getName($name): string {
    $key = $this->input_name;
    if (empty($key) || $this->method == "get") {
      return $name;
    } else {
      foreach ($key as $i => &$k) {
        if ($i > 0) {
          $k = "[".$k."]";
        }
      }
      $key = implode("", $key);
      if (false !== strpos($name, "[")) {
        $parts = explode("[", $name, 2);
        return $key."[".$parts[0]."][".$parts[1];
      } else {
        return $key."[".$name."]";
      }
    }
  }

  /**
   * Get the POST or GET value from the relative name.
   *
   * @param string $name the relative name
   *
   * @return string the GET or POST value
   */
  public function get($name) {
    $parts = explode("[", $name);
    $var = ($this->method == "post") ? $this->getPost() : $this->request->getQueryParams();
    foreach ($parts as $p) {
      if (is_array($var)) {
        $var = $var[rtrim($p, "]")] ?? null;
      }
    }
    if (is_array($var)) {
      return $var;
    } else {
      return stripslashes($var);
    }
  }

  /**
   * Set the GET or POST value.
   *
   * @param string $name the relative name
   * @param string $value the value
   */
  public function set($name, $value) {
    $parts = explode("[", $name);
    $key = array_pop($parts);
    $data = ($this->method == "post") ? $this->getPost() : $this->request->getQueryParams();
    $var = &$data;
    foreach ($parts as $p) {
      $var = &$var[rtrim($p, "]")];
    }
    $var[$key] = $value;

    if ($this->method == "post") {
      $this->setPost($data);
    } else {
      $this->request = $this->request->withQueryParams($data);
    }
    return $value;
  }

  /**
   * Converts the option string given to form elements into an array and sets up default values
   */
  public function fillOps(&$ops, $control = "") {
    $name = array_shift($ops);
    if (empty($ops['name'])) {
      $ops['name'] = $name;
    }
    // model
    if (empty($ops['model'])) {
      $ops['model'] = $this->model;
    }
    // id, label, and class
    if (empty($ops['id'])) {
      $ops['id'] = $ops['name'];
    }
    $ops['nolabel'] = (isset($ops['nolabel'])) ? true : false;
    if (empty($ops['label'])) {
      $ops['label'] = ucwords(str_replace("_", " ", $ops['name']));
    }
    $ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ").$ops['name']."-field";
    if (in_array($control, ["autocomplete", "category_select", "file_select", "select", "tag_select", "textarea", "file", "input", "password", "text"])) {
      $ops['class'] .= " form-control";
    }
  }

  public function assign($key, $value = null) {
    if (is_array($key)) {
      foreach ($key as $k => $v) {
        $this->assign($k, $v);
      }
    } else {
      $this->vars[$key] = $value;
    }
  }

  /**
   * Generate a form control (a tag with a name attribute such as input, select, textarea, file).
   *
   * @param string $control the name of the form control, usually the tag (input, select, textarea, file)
   * @param array $field the attributes for the html tag - special ones below
   *                  name: the relative name, eg. 'group[]' might become 'users[group][]'
   */
  public function formControl($control, $field) {
    $this->vars = ["display" => $this];
    $this->fillOps($field, $control);
    // run filters
    if ($hook = $this->hookFactory->get($control)) {
      $hook->build($this, $control, $field);
    }

    $capture = "field";
    if (empty($field['field'])) {
      $field['field'] = explode("[", $field['name'])[0];
    }
    $field['name'] = $this->getName($field['name']);
    foreach ($field as $k => $v) {
      $this->assign($k, $v);
    }
    if (isset($field['nofield'])) {
      unset($field['nofield']);
      $capture = $control;
    }
    $this->assign("attributes", $field);
    $this->assign("control", $control);
    return $this->output->capture([$field['model']."/form/$field[field]-$capture.html", "form/$field[field]-$capture.html", $field['model']."/form/$capture.html", "form/$capture.html"], $this->vars);
  }

  public function __call($name, $arguments) {
    return $this->formControl($name, $arguments[0]);
  }
}
