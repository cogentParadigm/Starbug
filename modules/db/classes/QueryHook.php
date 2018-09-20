<?php
namespace Starbug\Core;
class QueryHook {
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
  public function empty_before_insert($query, $column, $argument) {
  }
  /**
   * This hook is invoked when updating and the field has not been specified.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function empty_before_update($query, $column, $argument) {
  }
  /**
   * This hook is invoked when inserting or updating and the field has not been specified.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function empty_validate($query, $column, $argument) {
  }
  /**
   * This hook is invoked when inserting and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function before_insert($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function before_update($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked before deletion.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function before_delete($query, $column, $argument) {
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
  public function after_insert($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function after_update($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked when inserting or updating and a value has been specified.
   * The value must be returned.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function after_store($query, $key, $value, $column, $argument) {
    return $value;
  }
  /**
   * This hook is invoked after deletion.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function after_delete($query, $column, $argument) {
  }
}
