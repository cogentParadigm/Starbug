<?php
namespace Starbug\Files\Command;

use PDO;
use Starbug\Db\DatabaseInterface;

class CleanupDatabase {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }

  /**
   * Execute the command to scan and cleanup orphaned file records in the database.
   *
   * @param array $argv Command line arguments
   */
  public function __invoke($positional = []) {
    echo "Scanning database for orphaned file records...\n";
    try {
      // Find tables with foreign keys to files table
      $foreignKeyTables = $this->findTablesWithFileReferences();
      if (empty($foreignKeyTables)) {
        echo "No tables with file references found.\n";
        return;
      }

      // Collect all orphaned file IDs
      $orphanedFiles = $this->findOrphanedFileRecords($foreignKeyTables);
      if (empty($orphanedFiles)) {
        echo "No orphaned file records found.\n";
        return;
      }
      echo "Found " . count($orphanedFiles) . " orphaned file records:\n";
      foreach ($orphanedFiles as $file) {
        echo "  - File: " . $file["id"]."_".$file["filename"] . "\n";
      }

      echo "\nDo you want to delete these orphaned file records? (y/N): ";
      $handle = fopen("php://stdin", "r");
      $line = trim(fgets($handle));
      if (strtolower($line) === 'y') {
        $deleted = $this->deleteOrphanedFiles(array_column($orphanedFiles, "id"));
        echo "Successfully deleted " . $deleted . " orphaned file records.\n";
      } else {
        echo "Operation cancelled. No file records were deleted.\n";
      }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
  }

  protected function findTablesWithFileReferences(): array {
    $config = $this->db->getConnection()->getParams();
    $db = $config["dbname"];
    $prefix = $this->db->prefix("");
    $table = $prefix."files";
    $sql = "SELECT table_name, column_name ".
      "FROM information_schema.key_column_usage ".
      "WHERE table_schema = '{$db}' AND referenced_table_name = '{$table}'";
    $refs = $this->db->exec($sql)->fetchAll(PDO::FETCH_ASSOC);
    $tables = [];
    foreach ($refs as $row) {
      $table = substr($row["table_name"], strlen($prefix));
      $tables[$table][] = $row["column_name"];
    }
    return $tables;
  }

  protected function findOrphanedFileRecords(array $foreignKeyTables): array {
    $query = $this->db->query("files")->select("files.id, files.filename");
    foreach ($foreignKeyTables as $table => $fileColumns) {
      foreach ($fileColumns as $column) {
        $subquery = $this->db->query($table)->select($column)->condition($column, "NULL", "!=");
        $query->condition("files.id", $subquery, "NOT IN");
      }
    }

    $orphans = $query->all();
    return $orphans;
  }

  protected function deleteOrphanedFiles(array $orphanedFileIds): int {
    if (empty($orphanedFileIds)) {
      return 0;
    }

    $deleted = 0;
    foreach ($orphanedFileIds as $fileId) {
      try {
        $result = $this->db->query('files')->condition('id', $fileId)->delete();
        if ($result) {
          $deleted++;
          echo "Deleted file record: " . $fileId . "\n";
        }
      } catch (\Exception $e) {
        echo "Failed to delete file record " . $fileId . ": " . $e->getMessage() . "\n";
      }
    }

    return $deleted;
  }
}
