<?php
class UserMigration extends Migration {

	function describe() {
		$fields = "first_name=\nstring,length=16\t,";
		$fields .= "last_name=\ntype=string,length=32\t,";
		$fields .= "password=\ntype=password\t,";
		$fields .= "email=\ntype=string,length=64,unique=true\t";
		return $fields;
	}

	function up() {
		$this->create_table("users", rA($this->describe()));
		$this->table_insert("users", "first_name, last_name, password, email, security", "'ali', 'g', '".md5("cg3:al1;")."', 'cogent@gmail.com', '4'");
	}

	function down() {
		$this->drop_table("users");
	}

}
?>