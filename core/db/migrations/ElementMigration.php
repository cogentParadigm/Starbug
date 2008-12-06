<?php
class ElementMigration extends Migration {

	function up() {
		$elementTable =  rA("template=\ntype=string\t,name=\ntype=string\t,visible=\ntype=small_int,default=1\t,importance=\ntype=small_int,default=0\t");
		$this->create_table("elements", $elementTable);
		$this->table_insert("elements", "name, security", "'Home', '0'");
		$this->table_insert("elements", "name, security", "'Login', '0'");
		$this->table_insert("elements", "name, security", "'Elements', '2'");
		$this->table_insert("elements", "name, security", "'Element', '4'");
		$this->table_insert("elements", "name, template, visible, security", "'Edit_element', 'Empty', '0', '4'");
		$this->table_insert("elements", "name, template, visible, security", "'Get_element', 'Empty', '0', '4'");
	}

	function down() {
		$this->drop_table("elements");
	}

}
?>