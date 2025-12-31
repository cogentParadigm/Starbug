<?php
namespace Starbug\Files\Command;

use League\Flysystem\MountManager;
use Starbug\Core\Storage\FilesystemInterface;
use Starbug\Db\DatabaseInterface;

class CleanupFilesystem {
  public function __construct(
    protected MountManager $filesystems,
    protected DatabaseInterface $db
  ) {
  }

  /**
   * Execute the command to scan and cleanup orphaned files.
   *
   * @param array $argv Command line arguments
   */
  public function __invoke($positional = []) {
    $filesystem = "default";
    if (!empty($positional)) {
      $filesystem = array_shift($positional);
    }

    echo "Scanning filesystem: " . $filesystem . "\n";

    try {
      // Get the filesystem
      $fs = $this->filesystems->getFilesystem($filesystem);

      // Get files from database
      $files = $this->db->query("files")->select("id, filename, location")->all();
      $dbFiles = [];
      foreach ($files as $file) {
        if ($file["location"] === $filesystem) {
          $dbFiles[$file["id"]."_".$file["filename"]] = $file;
        }
      }

      // Get all files in the filesystem
      $fsFiles = $this->listFilesRecursively($fs, "");

      // Find files that exist in filesystem but not in database
      $orphanedFiles = array_filter($fsFiles, function ($path) use ($dbFiles) {
        return !isset($dbFiles[$path]);
      });

      if (empty($orphanedFiles)) {
        echo "No orphaned files found in the " . $filesystem . " filesystem.\n";
        return;
      }

      echo "Found " . count($orphanedFiles) . " orphaned files:\n";

      // Ask for confirmation before deleting
      foreach ($orphanedFiles as $path) {
        echo "  - " . $path . "\n";
      }

      echo "\nDo you want to delete these files? (y/N): ";
      $handle = fopen("php://stdin", "r");
      $line = trim(fgets($handle));

      if (strtolower($line) === 'y') {
        $deleted = 0;
        foreach ($orphanedFiles as $path) {
          try {
            $fs->delete($path);
            echo "Deleted: " . $path . "\n";
            $deleted++;
          } catch (\Exception $e) {
            echo "Failed to delete " . $path . ": " . $e->getMessage() . "\n";
          }
        }
        echo "Successfully deleted " . $deleted . " orphaned files.\n";
      } else {
        echo "Operation cancelled. No files were deleted.\n";
      }
    } catch (\Exception $e) {
      echo "Error: " . $e->getMessage() . "\n";
    }
  }

  /**
   * List all files in a filesystem recursively.
   *
   * @param FilesystemInterface $fs The filesystem to scan
   * @param string $directory The directory to scan
   *
   * @return array List of file paths
   */
  protected function listFilesRecursively($fs, $directory, $recurse = false) {
    $results = [];
    $contents = $fs->listContents($directory, false);

    foreach ($contents as $item) {
      $path = isset($item['path']) ? $item['path'] : '';

      if (isset($item['type']) && $item['type'] === 'file') {
        $results[] = $path;
      } elseif ($recurse && isset($item['type']) && $item['type'] === 'dir') {
        $subResults = $this->listFilesRecursively($fs, $path);
        $results = array_merge($results, $subResults);
      }
    }

    return $results;
  }
}
