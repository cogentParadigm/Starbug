<?php
namespace Starbug\Db\Query\Extensions;

use Starbug\Core\IdentityInterface;
use Starbug\Db\Query\BuilderInterface;
use Starbug\Db\Schema\SchemaInterface;

class Action {
  public function __construct(IdentityInterface $user, SchemaInterface $schema) {
    $this->user = $user;
    $this->schema = $schema;
  }
  public function action(BuilderInterface $query, array $arguments) {
    $action = array_shift($arguments);
    $collection = array_shift($arguments);
    if (empty($collection)) $collection = $query->getQuery()->getAlias();
    if ($query->has($collection)) {
      $type = $query->getQuery()->getTable($collection)->getName();
      $join = true;
    } else {
      $type = $collection;
      $join = false;
    }

    $base_type = $this->schema->getEntityRoot($type);
    $columns = $this->schema->getTable($type)->getColumns();
    $user_columns = $this->schema->getTable("users")->getColumns();

    if ($join) {
      //join permits - match table and action
      if (!$query->has("permits")) $query->innerJoin("permits")->on("'".$type."' LIKE permits.related_table && '".$action."' LIKE permits.action");
      //global or object permit
      $query->where("('global' LIKE permits.priv_type || (permits.priv_type='object' && permits.related_id=".$collection.".id))");

      //determine what relationships the object must bear - defined by object_access fields
      foreach ($columns as $cname => $column) {
        if (isset($column['object_access'])) {
          if ($this->schema->hasTable($column['type'])) {
            //multiple reference
            $object_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
            $permit_field = "object_".$cname;
            $ref = $cname."_id";
            $target = ($type == $column['entity']) ? "id" : $column['entity']."_id";
            $query->condition(
              $query->createOrCondition()
                ->condition("permits.".$permit_field, "NULL")
                ->condition(
                  "permits.".$permit_field,
                  $query->query($object_table." as o")
                    ->select($ref)
                    ->where("o.".$column['entity']."_id=".$collection.".".$target)
                )
            );
          } else {
            //single reference
            $object_field = $cname;
            $permit_field = "object_".$cname;
            $query->where("(permits.".$permit_field." is null || permits.".$permit_field."=".$collection.".".$object_field.")");
          }
        }
      }
    } else {
      //table permit
      $query->where("'table' LIKE permits.priv_type && '".$type."' LIKE permits.related_table && '".$action."' LIKE permits.action");
    }

    //determine what relationships the user must bear - defined by user_access fields
    foreach ($user_columns as $cname => $column) {
      if (isset($column['user_access'])) {
        $permit_field = "user_".$cname;
        if (!$this->user->loggedIn()) {
          $query->condition("permits.".$permit_field, "NULL");
        } elseif ($this->schema->hasTable($column['type'])) {
          //multiple reference
          $user_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
          $ref = $cname."_id";
          $query->condition(
            $query->createOrCondition()
              ->condition("permits.".$permit_field, "NULL")
              ->condition(
                "permits.".$permit_field,
                $query->query($user_table." as u")
                  ->select($ref)->condition("u.users_id", $this->user->userinfo("id"))
              )
          );
        } else {
          //single reference
          $user_field = $cname;
          $query->condition(
            $query->createOrCondition()
              ->condition("permits.".$permit_field, "NULL")
              ->condition(
                "permits.".$permit_field,
                $query->query("users as u")
                  ->select($user_field)->condition("u.id", $this->user->userinfo("id"))
              )
          );
        }
      }
    }

    //generate a condition for each role a permit can have. One of these must be satisfied
    $roleConditions = $query->createOrCondition();
    //everyone - no restriction
    $roleConditions->where("permits.role='everyone'");
    //user - a specific user
    $roleConditions->where("permits.role='user' && permits.who='".$this->user->userinfo("id")."'");

    if ($join) {
      //self - permit for user actions
      if ($type == "users") $roleConditions->where("permits.role='self' && ".$collection.".id='".$this->user->userinfo("id")."'");
      //owner - grant access to owner of object
      $roleConditions->where("permits.role='owner' && ".$collection.".owner='".$this->user->userinfo("id")."'");
      //[user_access field] - requires users and objects to share the same terms for the given relationship
      foreach ($user_columns as $cname => $column) {
        if (isset($column['user_access']) && isset($columns[$cname])) {
          if ($this->schema->hasTable($column['type'])) {
            //multiple reference
            $user_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
            $object_table = empty($columns[$cname]['table']) ? $columns[$cname]['entity']."_".$cname : $columns[$cname]['table'];
            $ref = $cname."_id";
            $target = ($type == $columns[$cname]['entity']) ? "id" : $columns[$cname]['entity']."_id";
            if ($this->user->loggedIn()) {
              $roleConditions->condition(
                $query->createCondition()
                  ->condition("permits.role", $cname)
                  ->condition(
                    $query->createOrCondition()
                    ->condition(
                      $query->query($object_table." as o")->select($ref)
                      ->where("o.".$columns[$cname]['entity']."_id=".$collection.".".$target)
                      ->condition("o.".$ref, $query->query($user_table." as u")->select($ref)->condition("u.users_id", $this->user->userinfo("id"))),
                      "",
                      "EXISTS"
                    )
                    ->condition(
                      $query->query($object_table." as o")->select($ref)->where("o.".$columns[$cname]['entity']."_id=".$collection.".".$target),
                      "",
                      "NOT EXISTS"
                    )
                  )
              );
              /*
              $query->orWhere(
                "permits.role='".$cname."' && (EXISTS (".
                  "SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target." && o.".$ref." IN (".
                    "SELECT ".$ref." FROM ".$this->db->prefix($user_table)." u WHERE u.users_id=".$this->user->userinfo("id").
                  ")".
                ") || NOT EXISTS (SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target."))"
              );
              */
            } else {
              $roleConditions->condition(
                $query->createCondition()
                  ->condition("permits.role", $cname)
                  ->condition(
                    $query->query($object_table." o")->select($ref)->where("o.".$columns[$cname]['entity']."_id=".$collection.".".$target),
                    "",
                    "NOT EXISTS"
                  )
              );
              //$query->orWhere("permits.role='".$cname."' && NOT EXISTS (SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target.")");
            }
          } else {
            //single reference
            if ($this->user->loggedIn()) {
              $roleConditions->condition(
                $query->createCondition()
                  ->condition("permits.role", $cname)
                  ->condition(
                    $query->createOrCondition()
                      ->condition($collection.".".$cname, "NULL")
                      ->condition(
                        $collection.".".$cname,
                        $query->query("users")->select($cname)->condition("id", $this->user->userinfo("id"))
                      )
                  )
              );
            } else {
              $roleConditions->where("permits.role='".$cname."' && ".$collection.".".$cname." is null");
            }
          }
        }
      }
    }
    $query->condition($roleConditions);
  }
}