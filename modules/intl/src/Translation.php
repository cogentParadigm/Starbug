<?php
namespace Starbug\Intl;

use Starbug\Core\DatabaseInterface;

class Translation implements TranslationInterface {
  // @var DatabaseInterface
  protected $db;
  // @var string
  protected $language;
  public function __construct(DatabaseInterface $db, $language = "en") {
    $this->db = $db;
    $this->language = $language;
  }
  public function get($name, $target_language = "") {
    static $strings;
    if (empty($target_language)) $target_language = $this->language;
    if (!isset($strings[$target_language])) $strings[$target_language] = [];
    elseif (isset($strings[$target_language][$name])) return $strings[$target_language][$name];

    // no previously queried match was found, lets look for the translation in the target language
    $match = $this->db->query("strings")
                ->condition("strings.language", $target_language)
                ->condition("strings.name", $name)->one();
    if ($match) {
      $strings[$target_language][$name] = empty($match['value']) ? $name : $match['value'];
    } else {
      // no translation is available. Let's see if there is a default translation
      $source = $this->db->query("strings")
                  ->condition("strings.name", $name)
                  ->condition("strings.source", "1")->one();
      if ($source) {
        $strings[$target_language][$name] = empty($source['value']) ? $name : $source['value'];
      } else {
        $strings[$target_language][$name] = $name;
      }
    }

    return $strings[$target_language][$name];
  }
}
