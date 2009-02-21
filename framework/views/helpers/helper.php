<?php

class Framework_Views_Helpers_Helper {

	public function url($url) {
		if ($url[0] == '/') {
			$prefix = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/', 1));
			$url = $prefix . $url;
		}
		return $url;
	}

}

?>
