<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemerInterface;

class SetupCommand {
  public function __construct(DatabaseInterface $db, SchemerInterface $schemer, $base_directory) {
    $this->db = $db;
    $this->schemer = $schemer;
    $this->base_directory = $base_directory;
  }
  public function run($argv) {
    // CREATE FOLDERS & SET FILE PERMISSIONS
    $dirs = ["var", "var/models", "var/tmp", "var/public", "var/log", "var/public/uploads"];
    foreach ($dirs as $dir) if (!file_exists($this->base_directory."/".$dir)) exec("mkdir ".$this->base_directory."/".$dir);
    exec("chmod -R a+w ".$this->base_directory."/var");

    // INIT TABLES
    $this->schemer->migrate();

    $root_user = $this->db->query("users")->condition("email", "root")->one();
    if (empty($root_user['password']) || $root_user['modified'] === $root_user['created']) { // PASSWORD HAS NEVER BEEN UPDATED
      // COLLECT USER INPUT
      fwrite(STDOUT, "\nPlease choose a root password:");
      $admin_pass = str_replace("\n", "", fgets(STDIN));
      fwrite(STDOUT, "\n\nYou may log in with these credentials -");
      fwrite(STDOUT, "\nusername: root");
      fwrite(STDOUT, "\npassword: $admin_pass\n\n");
      // UPDATE PASSWORD
      $errors = $this->db->store("users", ["id" => $root_user["id"], "password" => $admin_pass, "groups" => "root,admin"]);
    }
  }
}
