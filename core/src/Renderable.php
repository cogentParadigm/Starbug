<?php
namespace Starbug\Core;

class Renderable {

  public $tagName = 'div';
  public $selfClosing = false;
  public $attributes = [];
  public $childNodes = [];
  public $textContent = "";
  public $parentNode = null;
  public $innerHTML = "";

  public $vars = [];


  public function __construct($selector, $children = [], $content = "") {
    // get tag name and attributes
    if (!is_array($selector)) {
      $selector = $this->parseSelector($selector);
    }
    if (!empty($selector['tag'])) {
      $this->tagName = $selector['tag'];
    }
    if (!empty($selector['attributes'])) {
      $this->attributes = $selector['attributes'];
    }
    // get content
    if ($content instanceof Renderable) {
      $content = (string) $content;
    }
    $this->textContent = $content;
  }

  /**
   * Assign a variable.
   *
   * @param string $key variable name
   * @param string $value variable value
   */
  public function assign($key, $value) {
    $args = func_get_args();
    if (count($args) == 1 && is_array($args[0])) {
      $args = $args[0];
      foreach ($args as $k => $v) {
        $this->vars[$k] = $v;
      }
    } else {
      $this->vars[$key] = $value;
    }
  }

  /**
   * Append a node.
   *
   * @param Renderable $node
   */
  public function appendChild($node) {
    if ($node instanceof Renderable) {
      $node->parentNode = $this;
    }
    $this->children[] = $node;
  }

  /**
   * Insert a node before a reference node.
   *
   * @param Renderable $node the node to insert
   * @param Renderable $target the node that $node will be inserted before
   */
  public function insertBefore($node, $target) {
    $position = array_search($target, $this->children);
    if (false !== $position) {
      $before = array_slice($this->children, 0, $position);
      $after = array_slice($this->children, $position);
      $this->children = array_merge($before, [$node], $after);
    }
  }

  /**
   * Set text content.
   *
   * @param string $text
   */
  public function setText($text) {
    $this->textContent = $text;
  }

  /**
   * Add a class.
   *
   * @param string $class
   */
  public function addClass($class) {
    $position = array_search($class, $this->attributes['class']);
    if (false === $position) {
      $this->attributes['class'][] = $class;
    }
  }

  /**
   * Remove a class.
   *
   * @param string $class
   */
  public function removeClass($class) {
    $position = array_search($class, $this->attributes['class']);
    if (false !== $position) {
      unset($this->attributes['class'][$position]);
    }
  }

  /**
   * Check if a class is already applied
   *
   * @param string $class
   */
  public function hasClass($class) {
    $position = array_search($class, $this->attributes['class']);
    return (false !== $position);
  }

  /**
   * Make this castable to string.
   */
  public function __toString() {
    // Opening tag.
    $value = "<".$this->tagName;
    foreach ($this->attributes as $k => $v) {
      if (empty($v)) {
        unset($this->attributes[$k]);
      }
    }
    if (!empty($this->attributes)) {
      foreach (["class"] as $attr) {
        if (!empty($this->attributes[$attr])) {
          $this->attributes[$attr] = implode(" ", $this->attributes[$attr]);
        }
      }
      $value .= $this->attributes($this->attributes);
    }
    // self closing tags
    if ($this->selfClosing) {
      $value .= "/>";
      return $value;
    }
    $value .= ">";

    // content
    if (!empty($this->textContent)) {
      $value .= htmlspecialchars($this->textContent, ENT_QUOTES, 'UTF-8');
    }
    if (!empty($this->children)) {
      foreach ($this->children as $child) {
        $value .= (string) $child;
      }
    }
    if (!empty($this->innerHTML)) {
      $value .= $this->innerHTML;
    }

    // closing tag
    $value .= "</".$this->tagName.">";
    return $value;
  }

  /**
   * Convert a CSS selector to a regex pattern.
   *
   * @param string $selector css selector
   *
   * @return string regex pattern
   */
  public static function parseSelector($selector) {
    $pattern = '/^(?P<type>[\*|\w|\-]+)?(?P<id>#[\w|\-]+)?(?P<classes>\.[\w|\-|\.]+)*(?P<data>\[.+\])*$/';
    preg_match($pattern, $selector, $matches);
    $tag = $matches['type'];
    $attributes = ["class" => []];
    if (!empty($matches['id'])) {
      $attributes['id'] = substr($matches['id'], 1);
    }
    if (!empty($matches['classes'])) {
      $attributes['class'] = explode(".", substr($matches['classes'], 1));
    }
    if (!empty($matches['data'])) {
      $parts = explode("][", trim($matches['data'], "[]"));
      foreach ($parts as $attr) {
        $attr = explode("=", $attr);
        $attributes[$attr[0]] = trim($attr[1], '"');
      }
    }
    return ["tag" => $tag, "attributes" => $attributes];
  }
  protected function attributes($attributes) {
    $valid = ["abbr", "accept-charset", "accept", "accesskey", "action", "align", "alink", "alt", "archive", "autocomplete", "axis", "background", "bgcolor", "cellpadding", "cellspacing", "char", "charoff", "charset", "checked", "cite", "class", "classid", "clear", "code", "codebase", "codetype", "color", "cols", "colspan", "compact", "content", "contenteditable", "contextmenu", "coords", "datetime", "declare", "defer", "dir", "disabled", "draggable", "dropzone", "enctype", "face", "for", "frame", "frameborder", "headers", "height", "hidden", "href", "hreflang", "hspace", "http-equiv", "id", "ismap", "label", "lang", "language", "link", "longdesc", "marginheight", "marginwidth", "maxlength", "media", "method", "multiple", "name", "nohref", "noresize", "noshade", "nowrap", "object", "placeholder", "profile", "prompt", "readonly", "rel", "rev", "rows", "rowspan", "rules", "scheme", "scope", "scrolling", "selected", "shape", "size", "span", "spellcheck", "src", "standby", "start", "style", "summary", "tabindex", "target", "text", "title", "type", "usemap", "valign", "value", "valuetype", "version", "vlink", "vspace", "width"];
    $validate = true;
    $filtered = "";
    foreach ($attributes as $k => $v) {
      if (!is_array($v) && (!$validate || (in_array($k, $valid) || (0===strpos($k, "on")) || (0===strpos($k, "data"))))) {
        $filtered .= " $k=\"$v\"";
      }
    }
    return $filtered;
  }
  public static function create($parent, $selector = "", $content = "") {
    if (!($parent instanceof Renderable)) {
      $content = $selector;
      $selector = $parent;
      $parent = null;
    }

    $selector = Renderable::parseSelector($selector);
    if (empty($selector['tag'])) {
      $node = $parent;
      $node->attributes = array_merge($node->attributes, $selector['attributes']);
    } else {
      $node = new Renderable($selector);
      if ($parent) {
        $parent->appendChild($node);
      }
    }

    $node->setText($content);

    return $node;
  }
}
