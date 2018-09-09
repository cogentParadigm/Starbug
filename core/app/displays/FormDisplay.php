<?php
namespace Starbug\Core;
class FormDisplay extends ItemDisplay {
  public $type = "form";
  public $template = "form.html";
  public $collection = "Form";
  public $input_name = false;

  public $url;
  public $method = "post";
  public $errors = [];
  public $layout;
  public $default_action = "create";
  public $submit_label = "Save";
  public $cancel_url = "";
  public $actions;
  protected $vars = [];
  public $horizontal = false;

  function __construct(TemplateInterface $output, ResponseInterface $response, HookFactoryInterface $hooks, DisplayFactoryInterface $displays, RequestInterface $request, DatabaseInterface $db, ModelFactoryInterface $models, CollectionFactoryInterface $collections) {
    $this->output = $output;
    $this->response = $response;
    $this->hook_builder = $hooks;
    $this->displays = $displays;
    $this->request = $request;
    $this->db = $db;
    $this->models = $models;
    $this->collections = $collections;
  }

  function build($options = []) {
    $this->options = $options;
    if (empty($this->model) && !empty($this->options['model'])) $this->model = $this->options['model'];
    if (!empty($options["input_name"])) $this->input_name = $options["input_name"];
    if (false === $this->input_name) $this->input_name = [$this->model];
    // grab schema
    if (!empty($this->model) && $this->models->has($this->model)) {
      $this->schema = $this->models->get($this->model)->hooks;
    }

    //create layout display
    $this->layout = $this->displays->get("LayoutDisplay");
    //create actions display
    $this->actions = $this->displays->get("ItemDisplay");
    $this->actions->add([$this->default_action, "label" => $this->submit_label, "class" => "btn-success"]);

    //run query
    $this->before_query($options);
    $this->query();
    $this->build_display($options);
  }

  /**
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  protected function before_query($options) {
    //override this method if needed
  }

  /**
   * filter columns to set the input type and some other defaults
   */
  function filter($field, $options, $column) {
    if (empty($options['input_type'])) {
      if ($column["type"] == "text") $options["input_type"] = "textarea";
      elseif ($column['type'] == "password") $options['input_type'] = "password";
      elseif ($column['type'] == "bool") $options['input_type'] = "checkbox";
      elseif ($column['type'] == "category") $options['input_type'] = "category_select";
      elseif ($column['type'] == "tags") $options['input_type'] = "tag_input";
      elseif (isset($column['upload'])) $options['input_type'] = "file_select";
      elseif ($column['type'] == "terms") $options['input_type'] = "multiple_category_select";
      elseif ($this->models->has($column['type'])) $options['input_type'] = "multiple_select";
      elseif (isset($column['references'])) $options['input_type'] = "select";
      else $options['input_type'] = "text";
    }
    if ($options['input_type'] == "password") $options['class'] .= ((empty($options['class'])) ? "" : " ")."text";
    elseif ($column['type'] == "bool" && $options["input_type"] == "checkbox") $options['value'] = 1;
    elseif ($options['input_type'] == "datetime") $options['data-dojo-type'] = "starbug/form/DateTextBox";
    elseif ($options['input_type'] == "crud") {
      if (empty($options['table'])) $options['table'] = (empty($column['table'])) ? $options['model']."_".$field : $column['table'];
    } elseif ($options['input_type'] == "category_select") {
      if (empty($options['taxonomy'])) $options['taxonomy'] = (empty($column['taxonomy'])) ? $options['model']."_".$field : $column['taxonomy'];
    }
    if (!isset($options['required'])) {
      $default = isset($column['default']);
      $optional = isset($column['optional']);
      $nullable = isset($column['null']);
      $not_optional_update = (!isset($column['optional_update']) || empty($object_id));
      if (!$default && !$optional && !$nullable && $not_optional_update) {
          $options['required'] = true;
      } else {
        $options['required'] = false;
      }
    }
    return $options;
  }

  /**
   * override query function to only query with id
   */
  function query($options = null) {
    //set options
    if (is_null($options)) $options = $this->options;

    if (empty($options['id'])) $this->items = [];
    else parent::query(["action" => $this->default_action] + $options);

    if ($this->request->hasParameter('copy') && is_numeric($this->request->getParameter('copy')) && empty($this->items)) {
      $options['id'] = $this->request->getParameter('copy');
      parent::query(["action" => $this->default_action] + $options);
      if (!empty($this->items)) {
        unset($this->items[0]['id']);
      }
    }

    //load POST data
    if (!empty($this->items)) {
      if (!$this->hasPost()) $this->setPost([]);
      foreach ($this->items[0] as $k => $v) {
        if (!$this->hasPost($k)) $this->setPost($k, $v);
      }
    }
  }

  function before_render() {
    // set form attributes
    $this->attributes["action"] = $this->url;
    $this->attributes["method"] = $this->method;
    $this->attributes["accept-charset"] = "UTF-8";
    if (!empty($this->model) && !empty($this->default_action)) {
      if ($this->success($this->default_action)) $this->attributes['class'][] = "submitted";
      elseif ($this->failure($this->default_action)) $this->attributes['class'][] = "errors";
    }
    // grab errors and update schema
    $this->errors = [];
    foreach ($this->fields as $name => $field) {
      $this->schema[$name] = $this->models->get($field['model'])->column_info($name);
      $error_key = implode(".", $this->input_name);
      $error_key .= ".".str_replace(["][", "[", "]"], [".", ".", ""], $name);
      $errors = $this->db->errors($error_key, true);
      if (!empty($errors)) $this->errors[$name] = $errors;
    }
  }

  function render($query = false) {
    parent::render($query);
  }

  public function errors($key = "", $values = false, $model = "") {
    if (empty($model)) $model = $this->model;
    return $this->models->get($model)->errors($key, $values);
  }

  public function error($error, $field = "global", $model = "") {
    if (empty($model)) $model = $this->model;
    $this->models->get($model)->error($error, $field);
  }

  public function success($action) {
    $args = func_get_args();
    if (count($args) == 1) $args = [$this->model, $args[0]];
    return $this->models->get($args[0])->success($args[1]);
  }

  public function failure($action) {
    $args = func_get_args();
    if (count($args) == 1) $args = [$this->model, $args[0]];
    return $this->models->get($args[0])->failure($args[1]);
  }

  public function hasPost() {
    $args = func_get_args();
    $keys = array_merge($this->input_name, $args);
    return call_user_func_array([$this->request, "hasPost"], $keys);
  }

  public function getPost() {
    $args = func_get_args();
    $keys = array_merge($this->input_name, $args);
    return call_user_func_array([$this->request, "getPost"], $keys);
  }

  public function setPost($value) {
    $args = func_get_args();
    $keys = array_merge($this->input_name, $args);
    return call_user_func_array([$this->request, "setPost"], $keys);
  }

  /**
   * get the full name attribute
   * eg. name becomes users[name]
   * eg. name[] becomes users[name][]
   * @param string $name the relative name
   * @return the full name
   */
  function get_name($name) {
    $key = $this->input_name;
    if (empty($key) || $this->method == "get") return $name;
    else {
      foreach ($key as $i => &$k) {
        if ($i > 0) $k = "[".$k."]";
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
   * get the POST or GET value from the relative name
   * @param string $name the relative name
   * @return string the GET or POST value
   */
  function get($name) {
    $keys = $this->input_name;
    $parts = explode("[", $name);
    $var = ($this->method == "post") ? $this->getPost() : $this->request->getParameters();
    foreach ($parts as $p) if (is_array($var)) $var = $var[rtrim($p, "]")];
    if (is_array($var)) return $var;
    else return stripslashes($var);
  }

  /**
   * set the GET or POST value
   * @param string $name the relative name
   * @param string $value the value
   */
  function set($name, $value) {
    $parts = explode("[", $name);
    $key = array_pop($parts);
    $data = ($this->method == "post") ? $this->getPost() : $this->request->getParameters();
    foreach ($parts as $p) {
      $var = &$var[rtrim($p, "]")];
    }
    $var[$key] = $value;

    if ($this->method == "post") {
      $this->setPost($data);
    } else {
      $this->request->setParameters($data);
    }
    return $value;
  }

  /**
   * converts the option string given to form elements into an array and sets up default values
   * @param star $ops the option string
   */
  function fill_ops(&$ops, $control = "") {
    $name = array_shift($ops);
    if (empty($ops['name'])) $ops['name'] = $name;
    //model
    if (empty($ops['model'])) $ops['model'] = $this->model;
    //id, label, and class
    if (empty($ops['id'])) $ops['id'] = $ops['name'];
    $ops['nolabel'] = (isset($ops['nolabel'])) ? true : false;
    if (empty($ops['label'])) $ops['label'] = ucwords(str_replace("_", " ", $ops['name']));
    $ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ").$ops['name']."-field";
    if (in_array($control, ["autocomplete", "category_select", "file_select", "select", "tag_select", "textarea", "file", "input", "password", "text"])) $ops['class'] .= " form-control";
  }

  function assign($key, $value = null) {
    if (is_array($key)) {
      foreach ($key as $k => $v) $this->assign($k, $v);
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
   * @param bool $self if true, will use a self closing tag. If false, will use an opening tag and a closing tag (default is false)
   */
  function form_control($control, $field) {
    $this->vars = ["display" => $this];
    $this->fill_ops($field, $control);
    //run filters
    $hooks = $this->hook_builder->get("form/".$control);
    foreach ($hooks as $hook) {
      $hook->build($this, $control, $field);
    }

    $capture = "field";
    if (empty($field['field'])) $field['field'] = reset(explode("[", $field['name']));
    $field['name'] = $this->get_name($field['name']);
    foreach ($field as $k => $v) $this->assign($k, $v);
    if (isset($field['nofield'])) {
      unset($field['nofield']);
      $capture = $control;
    }
    $this->assign("attributes", $field);
    $this->assign("control", $control);
    return $this->output->capture([$field['model']."/form/$field[field]-$capture.html", "form/$field[field]-$capture.html", $field['model']."/form/$capture.html", "form/$capture.html"], $this->vars);
  }

  function __call($name, $arguments) {
    if (empty($arguments[1])) $arguments[1] = [];
    return $this->form_control($name, $arguments[0], $arguments[1]);
  }
}
