<?php
namespace Starbug\Db\Query\Extensions;

use Starbug\Db\Query\BuilderInterface;
use Starbug\Db\Schema\SchemaInterface;

class Search {
  public function __construct(
    protected SchemaInterface $schema
  ) {
  }
  public function getSearchFields(BuilderInterface $builder, array $arguments) {
    $table = array_shift($arguments);
    return $this->schema->getTable($table)->getOption("search_fields");
  }
  /**
   * Add search conditions to search one or more fields for some words.
   *
   * Examples:
   * search string: 'beef and broccoli'
   * fields: 'name,description'
   * conditions: ((name LIKE '%beef%' OR description LIKE '%beef%') and (name LIKE '%broccoli%' OR description LIKE '%broccoli%'))
   *
   * @param string $keywords A natural language search string which can include operators 'and' and 'or' and quotes for exact matches.
   * @param string $fields A comma delimited list of columns to search on (you can escape a comma with a backslash).
   */
  public function search(BuilderInterface $builder, array $arguments) {
    list($keywords, $fields) = $arguments;
    // if there are no search terms or fields to search, there's nothing to do
    if (!empty($keywords) && !empty($fields)) {
      // split tokens (allowing escaped commas)
      $search_fields = preg_split('~(?<!\\\)' . preg_quote(",", '~') . '~', $fields);
      // unescape those commas
      foreach ($search_fields as $sfk => $sfv) {
        $search_fields[$sfk] = str_replace("\,", ",", $sfv);
      }
      // generate the conditions
      $builder->where($this->createSearchClause($keywords, $search_fields));
    }
  }
  /**
   * Build a search clause to be put into a WHERE clause.
   *
   * Examples:
   *
   * search string: 'beef and broccoli'
   * fields: array('name', 'description')
   * return: ((name LIKE '%beef%' OR description LIKE '%beef%') and (name LIKE '%broccoli%' OR description LIKE '%broccoli%'))
   *
   * @param string $text A natural language search string which can include operators 'and' and 'or' and quotes for exact matches.
   * @param array $fields A list of columns to search on.
   *
   * @return string SQL WHERE component.
   */
  protected function createSearchClause($text, $fields) {
    $text = strtolower(trim(str_replace("\\\"", "&quot;", $text)));
    // tokenize the text
    $output = [];
    $arr = explode("&quot;", $text);
    for ($i = 0; $i < count($arr); $i++) {
      if ($i % 2 == 0) {
        $output = array_merge($output, explode(" ", $arr[$i]));
      } else {
        $output[] = $arr[$i];
      }
    }
    $words = [];
    foreach ($output as $token) {
      if (trim($token) != "") {
        $words[] = $token;
      }
    }
    // generate condition string
    $conditions = "(";
    for ($word = 0; $word < count($words); $word++) {
      $w = $words[$word];
      if ($w!="") {
        if ($w!="and" && $w!="or") {
          $conditions .= "(";
          for ($field = 0; $field < count($fields); $field++) {
            $conditions .= $fields[$field]." LIKE '%".$w."%'";
            if ($field<(count($fields)-1)) {
              $conditions .= " OR ";
            } else {
              $conditions .= ")";
            }
          }
          if ($word < (count($words)-1)) {
            if ($words[$word+1] == "and" || $words[$word+1] == "or") {
              $conditions .= " ".$words[$word+1]." ";
            } else {
              $conditions .= " AND ";
            }
          }
        }
      }
    }
    $conditions .= ")";
    return $conditions;
  }
}
