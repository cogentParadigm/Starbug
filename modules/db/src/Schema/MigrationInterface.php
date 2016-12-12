<?php
namespace Starbug\Db\Schema;
interface MigrationInterface {
	public function up();
	public function down();
	public function migrate();
}
?>
