<?php 

	class ValidationHelpers {
	
		static function required($str) {
			if ( ! is_array($str)) {
				return (trim($str) == '') ? FALSE : TRUE;
			} else {
				return ( ! empty($str));
			}
		}

		static function valid_email($str) {
			return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
		}
	
		static function unique_username($str){
			$query = Symphony::Database()->fetchRow(0, "SELECT id FROM `tbl_authors` WHERE username = '{$str}'");
			return empty($query);
		}
	
	}
