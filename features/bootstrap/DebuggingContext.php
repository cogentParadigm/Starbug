<?php
namespace Starbug\Features;

use Starbug\Behat\Context\RawStarbugContext;

/**
 * Defines application features from the specific context.
 */
class DebuggingContext extends RawStarbugContext {
  /**
   * Wait time.
   *
   * @When /^(?:|I )wait for "(?P<count>[^"]*)" second(?:|s)$/
   */
  public function wait($count) {
    sleep($count);
  }
}
