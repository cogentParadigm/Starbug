<?php
namespace Starbug\Core;

class InputFilter implements InputFilterInterface {
  public function __construct() {
  }
  /**
   * Normalize a string.
   *
   * @param string $raw the raw string
   * @param string $valid_chars valid characters. default is 'a-zA-Z0-9'
   *
   * @return string the normalized version of $raw
   */
  public function normalize($raw, $valid_chars = 'a-zA-Z0-9 \-_') {
    return preg_replace("/[^".$valid_chars."]/", "", $raw);
  }
  public function boolean($boolean) {
    return filter_var($boolean, FILTER_VALIDATE_BOOLEAN);
  }
  public function int($int) {
    return filter_var($int, FILTER_VALIDATE_INT);
  }
  public function float($int) {
    return filter_var($int, FILTER_VALIDATE_FLOAT);
  }
  public function string($string) {
    return preg_replace('/  +/', ' ', strip_tags($string));
  }
  public function url($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
  }
  public function email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }
  public function plain($content) {
    return htmlentities(preg_replace('/  +/', ' ', $content), ENT_QUOTES, 'UTF-8');
  }
  /**
   * Convert an array to an HTML attribute string.
   *
   * @param array $attributes an associative array
   *
   * @return string the HTML attribute string
   */
  public function attributes($attributes) {
    $valid = ["abbr", "accept-charset", "accept", "accesskey", "action", "align", "alink", "alt", "archive", "autocomplete", "axis", "background", "bgcolor", "cellpadding", "cellspacing", "char", "charoff", "charset", "checked", "cite", "class", "classid", "clear", "code", "codebase", "codetype", "color", "cols", "colspan", "compact", "content", "contenteditable", "contextmenu", "coords", "datetime", "declare", "defer", "dir", "disabled", "draggable", "dropzone", "enctype", "face", "for", "formaction", "frame", "frameborder", "headers", "height", "hidden", "href", "hreflang", "hspace", "http-equiv", "id", "ismap", "label", "lang", "language", "link", "longdesc", "marginheight", "marginwidth", "maxlength", "media", "method", "multiple", "name", "nohref", "noresize", "noshade", "nowrap", "object", "placeholder", "profile", "prompt", "readonly", "rel", "rev", "rows", "rowspan", "rules", "scheme", "scope", "scrolling", "selected", "shape", "size", "span", "spellcheck", "src", "standby", "start", "style", "summary", "tabindex", "target", "text", "title", "type", "usemap", "valign", "value", "valuetype", "version", "vlink", "vspace", "width"];
    $validate = true;
    $filtered = "";
    foreach ($attributes as $k => $v) if (!is_array($v) && (!$validate || (in_array($k, $valid) || (0===strpos($k, "on")) || (0===strpos($k, "data"))))) $filtered .= " $k=\"$v\"";
    return $filtered;
  }

  public function html($content, $allowed = []) {
    $purifier = $this->createHtmlPurifier($allowed);
    return $purifier->purify($content);
  }
  protected function createHtmlPurifier($allowed = []) {
    if (empty($allowed)) {
      $allowed = [
        'img[src|alt|title|width|height|style|data-mce-src|data-mce-json]',
        'figure', 'figcaption',
        'video[src|type|width|height|poster|preload|controls]', 'source[src|type]',
        'a[href|target]',
        'iframe[width|height|src|frameborder|allowfullscreen]',
        'strong', 'b', 'i', 'u', 'em', 'br', 'font',
        'h1[style]', 'h2[style]', 'h3[style]', 'h4[style]', 'h5[style]', 'h6[style]',
        'p[style]', 'div[style]', 'center', 'address[style]',
        'span[style]', 'pre[style]',
        'ul', 'ol', 'li',
        'table[width|height|border|style]', 'th[width|height|border|style]',
        'tr[width|height|border|style]', 'td[width|height|border|style]',
        'hr'
      ];
    }
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
    $config->set('CSS.AllowTricky', true);
    $config->set('Cache.SerializerPath', '/tmp');

    // Allow iframes from:
    // o YouTube.com
    // o Vimeo.com
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');

    $config->set('HTML.Allowed', implode(',', $allowed));

    // Set some HTML5 properties
    $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
    $config->set('HTML.DefinitionRev', 1);

    if ($def = $config->maybeGetRawHTMLDefinition()) {
      // http://developers.whatwg.org/sections.html
      $def->addElement('section', 'Block', 'Flow', 'Common');
      $def->addElement('nav', 'Block', 'Flow', 'Common');
      $def->addElement('article', 'Block', 'Flow', 'Common');
      $def->addElement('aside', 'Block', 'Flow', 'Common');
      $def->addElement('header', 'Block', 'Flow', 'Common');
      $def->addElement('footer', 'Block', 'Flow', 'Common');

      // Content model actually excludes several tags, not modelled here
      $def->addElement('address', 'Block', 'Flow', 'Common');
      $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

      // http://developers.whatwg.org/grouping-content.html
      $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
      $def->addElement('figcaption', 'Inline', 'Flow', 'Common');

      // http://developers.whatwg.org/the-video-element.html#the-video-element
      $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
        'src' => 'URI',
        'type' => 'Text',
        'width' => 'Length',
        'height' => 'Length',
        'poster' => 'URI',
        'preload' => 'Enum#auto,metadata,none',
        'controls' => 'Bool',
      ]);
      $def->addElement('source', 'Block', 'Flow', 'Common', [
        'src' => 'URI',
        'type' => 'Text',
      ]);

      // http://developers.whatwg.org/text-level-semantics.html
      $def->addElement('s', 'Inline', 'Inline', 'Common');
      $def->addElement('var', 'Inline', 'Inline', 'Common');
      $def->addElement('sub', 'Inline', 'Inline', 'Common');
      $def->addElement('sup', 'Inline', 'Inline', 'Common');
      $def->addElement('mark', 'Inline', 'Inline', 'Common');
      $def->addElement('wbr', 'Inline', 'Empty', 'Core');

      // http://developers.whatwg.org/edits.html
      $def->addElement('ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);
      $def->addElement('del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);

      // TinyMCE
      $def->addAttribute('img', 'data-mce-src', 'Text');
      $def->addAttribute('img', 'data-mce-json', 'Text');

      // Others
      $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
      $def->addAttribute('table', 'height', 'Text');
      $def->addAttribute('td', 'border', 'Text');
      $def->addAttribute('th', 'border', 'Text');
      $def->addAttribute('tr', 'width', 'Text');
      $def->addAttribute('tr', 'height', 'Text');
      $def->addAttribute('tr', 'border', 'Text');
    }

    return new \HTMLPurifier($config);
  }
}
