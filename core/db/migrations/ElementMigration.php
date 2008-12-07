<?php
class ElementMigration extends Migration {

	function describe() {
		$fields = "template=\ntype=string,input_type=text,Label=Template\t,";
		$fields .= "name=\ntype=string,input_type=text,Label=Name\t,";
		$fields .= "visible=\ntype=small_int,default=1,input_type=select,label=Visible\t,";
		$fields .= "importance=\ntype=small_int,default=0,input_type=select,label=Importance\t";
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