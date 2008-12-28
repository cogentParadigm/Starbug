<?php
class ElementMigration extends Migration {

	function describe() {
		$fields = "path=\ntype=string,length=64\t,";
		$fields .= "template=\ntype=string,length=32\t,";
		$fields .= "visible=\ntype=int,length=2,default=1,input_type=select,range=0:1\t,";
		$fields .= "importance=\ntype=int,length=2,default=0,input_type=select,range=0:10\t";
		return $fields;
	}

	function up() {
		$elementTable =  rA($this->describe());
		$this->create_table("elements", $elementTable);
		$this->table_insert("elements", "path, template, security", "'elements', 'Starbug', '2'");
		$this->table_insert("elements", "path, template, visible, security", "'elements/new', 'Ajax', '0', '2'");
		$this->table_insert("elements", "path, template, visible, security", "'elements/get', 'Ajax', '0', '2'");
		$this->table_insert("elements", "path, template, visible, security", "'elements/edit', 'Ajax', '0', '2'");
	}

	function down() {
		$this->drop_table("elements");
	}

}
?>