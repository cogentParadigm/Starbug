<?php
namespace Starbug\Db\Query;
interface CompilerInterface {
	public function build(QueryInterface $query);
}
