<?php

	require_once 'class.validation_helpers.php';

	class Validation {
		
		private $_event_name;
		private $_response;

		private $_rules = array();
		private $_labels = array();
		private $_error_array = array();
		
		public function __construct($event_name) {
			$this->_event_name = $event_name;
		}
		
		public function setLabels(array $data){
			foreach ($data as $key => $val) {
				$this->_labels[$key] = $val;
			}
		}
		
		public function setRules(array $data) {
			foreach ($data as $key => $val) {
				$this->_rules[$key] = $val;
			}
		}
		
		public function label($field){
			if ( ! isset($this->_labels[$field]) || $this->_labels[$field] == '' ) {
				return $field;
			}
			
			return $this->_labels[$field];
		}
		
		public function run() {
			$fields = $_POST['fields'];
			
			if (count($fields) == 0 || count($this->_rules) == 0) {
				return false;
			}
			
			foreach ($this->_rules as $field => $rules) {
				$ex = explode('|', $rules);

				if (in_array('required', $ex, TRUE)) {
					if ( ! ValidationHelpers::required($fields[$field]) ) {
						$message = __("'%s' is a required field.", array($this->label($field)));
						$this->_error_array[$field] = $message;
						continue;
					}
				}
				
				if (in_array('valid_email', $ex, TRUE)) {
					if ( ! ValidationHelpers::valid_email($fields[$field]) ) {
						$message = __("'%s' contains invalid data. Please check the contents.", array($this->label($field)));
						$this->_error_array[$field] = $message;
						continue;
					}
				}
				
				if (in_array('unique_username', $ex, TRUE)) {
					if ( ! ValidationHelpers::unique_username($fields[$field])) {
						$message = __("'%s' must be unique.", array($this->label($field)));
						$this->_error_array[$field] = $message;
						continue;
					}
				}
				
			}
			
			return (count($this->_error_array) > 0 ? false : true);
		}
		
		public function response() {
			$result = new XMLElement($this->_event_name);
			
			$post_values = new XMLElement('post-values');
			$fields = $_POST['fields'];
			
			if ( ! empty($fields)) {
				General::array_to_xml($post_values, $fields, true);
			}
			
			if ( ! empty($this->_error_array)) {
				$result->appendChild($post_values);
				$result->setAttribute('result', 'error');
				$result->appendChild(new XMLElement('message', __('Entry encountered errors when saving.')));

				foreach($this->_error_array as $field => $message){
					$type = $fields[$field] == '' ? 'missing' : 'invalid';
					$field = new XMLElement($field);
					$field->setAttribute('type', $type);
					$field->setAttribute('message', $message);
					$result->appendChild($field);
				}
			} else {
				$result->setAttribute('result', 'success');
				$result->appendChild(new XMLElement('message', __('Entry created successfully.')));
			}

			return $result;
		}
		
	}
