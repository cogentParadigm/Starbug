<?php
namespace Starbug\Db\Query;

interface EntityInterface {

  /**
   * Query helper to provide a query with all tables joined and columns selected.
   *
   * @param string $entity the name of the entity.
   */
  public function query($entity);

  /**
   * Load an entity by id.
   *
   * @param int $id the id of the entity to load
   * @param boolean $reset set to true if you don't want to load from cache
   * @param string $name the name of the entity
   */
  public function load($name, $id, $reset = false);

  /**
   * Save an entity.
   *
   * @param array $fields the properties to save
   * @param array $from the conditions to match on instead of an ID. must map to a single entity
   * @param string $name the name of the entity
   */
  public function store($name, $fields, $from = []);

  /**
   * Delete an entity by id.
   *
   * @param string $name the entity name
   * @param int $id the id of the item to delete
   */
  public function remove($name, $id);
}
