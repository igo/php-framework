<?php

class Framework_Models_Validator {
	
	public static function blank($value) {
		$val = trim($value);
		return empty($val);
	}

	/**
	 * Check length of value
	 * @return bool
	 * @param object $value
	 * @param object $params min, max length
	 */
	public static function length($value, array $params) {
		if (isset($params['min']) && strlen($value) < $params['min']) {
			return false;
		}
		if (isset($params['max']) && strlen($value) > $params['max']) {
			return false;
		}
		return true;
	}
	

	public static function validate($value, $rule, array $params) {
		$result = Validator::$rule($value, $params);
		return $result;
	}

}

?>