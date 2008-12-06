<?php
class UserMigration extends Migration {

	function describe() {
		$fields = "first_name=\ntype=short_string,input_type=text,label=First Name\t,";
		$fields .= "last_name=\ntype=string,input_type=text,label=Last Name\t,";
		$fields .= "password=\ntype=string,input_type=password,label=Password\t,";
		$fields .= "email=\ntype=long_string,label=Email,unique=true\t";
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