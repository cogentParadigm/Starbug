<?php
namespace Starbug\Features;

use Starbug\Behat\Context\RawStarbugContext;
use Behat\Gherkin\Node\TableNode;
use Ingenerator\BehatTableAssert\TableParser\HTMLTable;
use Ingenerator\BehatTableAssert\AssertTable;

/**
 * Defines application features from the specific context.
 */
class CrudListContext extends RawStarbugContext {

  /**
   * Assert the presence of a grid widget.
   *
   * @Then I should see a paged grid with columns:
   */
  public function assertGrid(TableNode $expected) {
    $assert = $this->mink->assertSession();
    $table = $assert->elementExists("css", "table[data-dojo-type=\"starbug/grid/PagedGrid\"]")->getOuterHtml();
    $table = str_replace("</table>", "<tbody></tbody></table>", $table);
    $actual = HTMLTable::fromHTMLString($table);
    $assertTable = new AssertTable();
    $assertTable->isSame($expected, $actual);
  }

  /**
   * Follow the link to download a CSV export.
   * Used to generate behavior without javascript.
   *
   * @When I export :type by clicking :button
   */
  public function exportCsv($type, $button = "") {
    $this->mink->visit("api/".$type."/admin.csv");
  }
}
