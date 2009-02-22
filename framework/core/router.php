<?php

class Framework_Core_Router {

	public function route($apps) {
		// get path where we are running
		$url = parse_url($_SERVER['REQUEST_URI']);
//		if (isset($url['query'])) {
//			parse_str($url['query'], $variables);
//		} else {
//			$variables = array ();
//		}

		// remove parent directories
		$depth = count(explode('/', $_SERVER['SCRIPT_NAME'])) - 1;
		$offset = 1;
		for ($i = 0; $i < $depth; $i++) {
			$offset = strpos($url['path'], '/', $offset);
		}
		$path = substr($url['path'], $offset + 1);

		// remove last slash /
		//$path = substr($path, 0, -1);

		// run app
		foreach ($apps as $appConfig) {
			$app = new Framework_App_Application($appConfig['name']);
			if ($app->run($path) == 300) {
				return true;
			}
		}

		return false;
	}

}

?>