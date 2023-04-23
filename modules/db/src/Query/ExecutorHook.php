<?php
namespace Starbug\Db\Query;

/**
 * Hook interface for Starbug\Db\Query\Executor
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ExecutorHook {
  /**
   * This hook is invoked when called as a function on a query.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function query($query, $args = []) {
  }
  /**
   * This hook is invoked when inserting and the field has not been specified.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function emptyBeforeInsert($query, $column, $argument) {
  }
  /**
   * This hook is invoked when updating and the field has not been specified.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function emptyBeforeUpdate($query, $column, $argument) {
  }
  /**
   * This hook is invoked when inserting or updating and the field has not been specified.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function emptyValidate($query, $column, $argument) {
  }
  /**
   * This hook is invoked when inserting and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function beforeInsert($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function beforeUpdate($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked before deletion.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function beforeDelete($query, $column, $argument) {
  }
  /**
   * This hook is invoked when inserting or updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function validate($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when inserting and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function insert($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function update($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when inserting or updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function store($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when inserting and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function afterInsert($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function afterUpdate($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when inserting or updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function afterStore($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked after deletion.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function afterDelete($query, $column, $argument) {
  }
}
