<?php
namespace Starbug\Content\Tests\Acceptance\Admin;

use DI\Attribute\Inject;
use Starbug\Db\Testing\WebDatabaseTestCase;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Testing\Domain\Crud;
use Starbug\Users\Tests\Domain\Authentication;

/**
 * Web-based acceptance tests for page management.
 *
 * Covers page CRUD through the admin web UI.
 */
#[Fixture('app/fixtures/db/reset.yml')]
#[Fixture('modules/content/tests/fixtures/db/pages.yml')]
class PageManagementTest extends WebDatabaseTestCase {

  #[Inject]
  protected Authentication $auth;

  #[Inject]
  protected Crud $pages;

  protected function setUp(): void {
    parent::setUp();
    $this->auth->login('root', 'Mz2&cssMy@9tclan');
  }

  public function testListPages(): void {
    $this->pages->list();
  }

  public function testCreatePage(): void {
    $page = $this->pages->createAndMatch([
      'title' => 'PHPUnit'
    ], [
      'path' => 'phpunit',
      'blocks' => ['content-1' => 'test']
    ]);

    $alias = $this->assertRecordExists('aliases', ['id' => $page['path']]);
    $this->assertSame('phpunit', $alias['alias']);
    $this->assertRecordMatches('blocks', ['pages_id' => $page['id']], ['content' => 'test']);
  }

  public function testEditPage(): void {
    $page = $this->pages->create([
      'title' => 'PHPUnit',
      'path' => 'phpunit',
      'blocks' => ['content-1' => 'test']
    ]);

    $this->pages->updateAndMatch($page['id'], [
      'title' => 'Test Title'
    ], [
      'blocks' => ['content-1' => 'Test Content']
    ]);

    $this->assertRecordMatches('blocks', ['pages_id' => $page['id']], ['content' => 'Test Content']);
  }

  public function testDeletePage(): void {
    $page = $this->pages->create([
      'title' => 'PHPUnit',
      'path' => 'phpunit'
    ]);

    $this->assertRecordExists('pages', ['id' => $page['id']]);
    $this->pages->delete($page['id']);
    $this->assertRecordNotExists('pages', ['id' => $page['id']]);
    $this->assertRecordNotExists('blocks', ['pages_id' => $page['id']]);
  }
}
