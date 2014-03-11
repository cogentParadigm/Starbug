<?php
	class QueryHook extends Hook {
		function empty_before_insert(&$query, $column, $argument) {
			//this hook is invoked when inserting and the field has not been specified
		}
		
		function empty_before_update(&$query, $column, $argument) {
			//this hook is invoked when updating and the field has not been specified
		}
		
		function empty_validate(&$query, $column, $argument) {
			//this hook is invoked when inserting or updating and the field has not been specified
		}
		
		function before_insert(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when inserting and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function before_update(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when updating and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function validate(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when inserting or updating and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function insert(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when inserting and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function update(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when updating and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function store(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when inserting or updating and a value has been specified
			//the value must be returned
			return $value;
		}

		function after_insert(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when inserting and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function after_update(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when updating and a value has been specified
			//the value must be returned
			return $value;
		}
		
		function after_store(&$query, $key, $value, $column, $argument) {
			//this hook is invoked when inserting or updating and a value has been specified
			//the value must be returned
			return $value;
		}
		
	}
?>
