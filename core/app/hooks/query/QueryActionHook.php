<?php
namespace Starbug\Core;

class QueryActionHook extends QueryHook {
  public function __construct(IdentityInterface $user, DatabaseInterface $db, ModelFactoryInterface $models) {
    $this->user = $user;
    $this->db = $db;
    $this->models = $models;
  }
  public function query($query, $args = []) {
    $action = array_shift($args);
    $collection = array_shift($args);
    if (empty($collection)) $collection = $query->last_collection;
    if ($query->has($collection)) {
      $type = $query->query['from'][$collection];
      $join = true;
    } else {
      $type = $collection;
      $join = false;
    }

    $users = $this->models->get("users");
    $instance = $this->models->get($type);
    $base_type = $instance->root();
    $columns = $instance->columnInfo();
    $user_columns = $users->columnInfo();

    if ($join) {
      // join permits - match table and action
      if (!$query->has("permits")) $query->innerJoin("permits")->on("'".$type."' LIKE permits.related_table && '".$action."' LIKE permits.action");
      // global or object permit
      $query->where("('global' LIKE permits.priv_type || (permits.priv_type='object' && permits.related_id=".$collection.".id))");

      // determine what relationships the object must bear - defined by object_access fields
      foreach ($columns as $cname => $column) {
        if (isset($column['object_access'])) {
          if ($this->models->has($column['type'])) {
            // multiple reference
            $object_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
            $permit_field = "object_".$cname;
            $ref = $cname."_id";
            $target = ($type == $column['entity']) ? "id" : $column['entity']."_id";
            $query->where("(permits.".$permit_field." is null || permits.".$permit_field." IN (SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$column['entity']."_id=".$collection.".".$target."))");
          } else {
            // single reference
            $object_field = $cname;
            $permit_field = "object_".$cname;
            $query->where("(permits.".$permit_field." is null || permits.".$permit_field."=".$collection.".".$object_field.")");
          }
        }
      }
    } else {
      // table permit
      $query->where("'table' LIKE permits.priv_type && '".$type."' LIKE permits.related_table && '".$action."' LIKE permits.action");
    }

    // determine what relationships the user must bear - defined by user_access fields
    foreach ($user_columns as $cname => $column) {
      if (isset($column['user_access'])) {
        $permit_field = "user_".$cname;
        if (!$this->user->loggedIn()) {
          $query->where("permits.".$permit_field." is null");
        } elseif ($this->models->has($column['type'])) {
          // multiple reference
          $user_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
          $ref = $cname."_id";
          $query->where("(permits.".$permit_field." is null || permits.".$permit_field." IN (SELECT ".$ref." FROM ".$this->db->prefix($user_table)." u WHERE u.users_id=".$this->user->userinfo("id")."))");
        } else {
          // single reference
          $user_field = $cname;
          $query->where("(permits.".$permit_field." is null || permits.".$permit_field." IN (SELECT ".$user_field." FROM ".$this->db->prefix("users")." u WHERE u.id=".$this->user->userinfo("id")."))");
        }
      }
    }

    // generate a condition for each role a permit can have. One of these must be satisfied
    $query->open("roles");
    // everyone - no restriction
    $query->where("permits.role='everyone'");
    // user - a specific user
    $query->orWhere("permits.role='user' && permits.who='".$this->user->userinfo("id")."'");

    if ($join) {
      // self - permit for user actions
      if ($type == "users") $query->orWhere("permits.role='self' && ".$collection.".id='".$this->user->userinfo("id")."'");
      // owner - grant access to owner of object
      $query->orWhere("permits.role='owner' && ".$collection.".owner='".$this->user->userinfo("id")."'");
      // [user_access field] - requires users and objects to share the same terms for the given relationship
      foreach ($user_columns as $cname => $column) {
        if (isset($column['user_access']) && isset($columns[$cname])) {
          if ($this->models->has($column['type'])) {
            // multiple reference
            $user_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
            $object_table = empty($columns[$cname]['table']) ? $columns[$cname]['entity']."_".$cname : $columns[$cname]['table'];
            $ref = $cname."_id";
            $target = ($type == $columns[$cname]['entity']) ? "id" : $columns[$cname]['entity']."_id";
            if ($this->user->loggedIn()) {
              $query->orWhere(
                "permits.role='".$cname."' && (EXISTS (".
                  "SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target." && o.".$ref." IN (".
                    "SELECT ".$ref." FROM ".$this->db->prefix($user_table)." u WHERE u.users_id=".$this->user->userinfo("id").
                  ")".
                ") || NOT EXISTS (SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target."))"
              );
            } else {
              $query->orWhere("permits.role='".$cname."' && NOT EXISTS (SELECT ".$ref." FROM ".$this->db->prefix($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target.")");
            }
          } else {
            // single reference
            if ($this->user->loggedIn()) {
              $query->orWhere("permits.role='".$cname."' && (".$collection.".".$cname." is null || ".$collection.".".$cname." IN (SELECT ".$cname." FROM ".$this->db->prefix("users")." id=".$this->user->userinfo("id")."))");
            } else {
              $query->orWhere("permits.role='".$cname."' && ".$collection.".".$cname." is null");
            }
          }
        }
      }
    }
    $query->close();
  }
}
