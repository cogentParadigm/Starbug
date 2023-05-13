<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Db\DatabaseInterface;
use ZxcvbnPhp\Zxcvbn;

class StorePasswordHook extends ExecutorHook {
  public function __construct(SessionHandlerInterface $session, DatabaseInterface $db, Zxcvbn $zxcvbn, $passingScore, $performStrengthTests) {
    $this->session = $session;
    $this->db = $db;
    $this->zxcvbn = $zxcvbn;
    $this->passingScore = $passingScore;
    $this->performStrengthTests = $performStrengthTests;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (empty($value)) return $value;
    if ($this->performStrengthTests) {
      $passwordStrength = $this->zxcvbn->passwordStrength($value);
      if ($passwordStrength["score"] < $this->passingScore && !empty($passwordStrength["feedback"])) {
        if (!empty($passwordStrength["feedback"]["warning"])) {
          $errorMsg = ['<div>'.$passwordStrength["feedback"]["warning"].'</div>'];
        } else {
          $errorMsg = ['<div>There was an problem with your password.</div>'];
        }
        if (!empty($passwordStrength["feedback"]["suggestions"])) {
          $errorMsg[] = '<ul>';
          foreach ($passwordStrength["feedback"]["suggestions"] as $suggestion) {
            $errorMsg[] = '<li>' . $suggestion . '</li>';
          }
          $errorMsg[] = '</ul>';
        }
        $this->db->error(implode("", $errorMsg), "password", "users");
      }
    }
    return $this->session->hashPassword($value);
  }
}
