<?php
namespace Starbug\Core;
class QueryHook {
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function query($query, $args = array()) {
		//this hook is invoked when called as a function on a query.
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function empty_before_insert($query, $column, $argument) {
		//this hook is invoked when inserting and the field has not been specified
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function empty_before_update($query, $column, $argument) {
		//this hook is invoked when updating and the field has not been specified
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function empty_validate($query, $column, $argument) {
		//this hook is invoked when inserting or updating and the field has not been specified
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function before_insert($query, $key, $value, $column, $argument) {
		//this hook is invoked when inserting and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function before_update($query, $key, $value, $column, $argument) {
		//this hook is invoked when updating and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function before_delete($query, $column, $argument) {
		//this hook is invoked before deletion
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function validate($query, $key, $value, $column, $argument) {
		//this hook is invoked when inserting or updating and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function insert($query, $key, $value, $column, $argument) {
		//this hook is invoked when inserting and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function update($query, $key, $value, $column, $argument) {
		//this hook is invoked when updating and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function store($query, $key, $value, $column, $argument) {
		//this hook is invoked when inserting or updating and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function after_insert($query, $key, $value, $column, $argument) {
		//this hook is invoked when inserting and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function after_update($query, $key, $value, $column, $argument) {
		//this hook is invoked when updating and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function after_store($query, $key, $value, $column, $argument) {
		//this hook is invoked when inserting or updating and a value has been specified
		//the value must be returned
		return $value;
	}
	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function after_delete($query, $column, $argument) {
		//this hook is invoked after deletion
	}
}
