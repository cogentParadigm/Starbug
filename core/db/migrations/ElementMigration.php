<?php
class ElementMigration extends Migration {

	function describe() {
		$fields = "name=\ntype=string,length=32\t,";
		$fields .= "template=\ntype=string,length=32\t,";
		$fields .= "visible=\ntype=int,length=2,default=1\t,";
		$fields .= "importance=\ntype=int,length=2,default=0\t";
		return $fields;
	}

	function up() {
		$elementTable =  rA($this->describe());
		$this->create_table("elements", $elementTable);
		$this->table_insert("elements", "name, template, security", "'Elements', 'CoreApp', '2'");
		$this->table_insert("elements", "name, template, security", "'Element', 'CoreApp', '4'");
		$this->table_insert("elements", "name, template, visible, security", "'Edit_element', 'Empty', '0', '4'");
		$this->table_insert("elements", "name, template, visible, security", "'Get_element', 'Empty', '0', '4'");
	}

	function down() {
		$this->drop_table("elements");
	}

}
?>