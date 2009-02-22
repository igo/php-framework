<?php

class Framework_App_Application {

	public $urls;
	public $storage;
	public $appName;
	public $config;
	public $pdo = null;

	public function __construct($appName) {
		$this->appName = $appName;
		$this->load();
	}

	public function getModel($name) {
		if (can_import_class($name)) {
			return new $name($this->pdo, $this);
		} else if (can_import_class("{$this->appName}_Models_{$name}")) {
			$class = "{$this->appName}_Models_{$name}";
			return new $class($this->pdo, $this);
		}
		throw new Exception("Cannot load model {$name}.", 500);
	}

	public function load() {
		// load constants
		@include class_to_file($this->appName) . '/constants.php';

		// load urls
		$urls = array();
		include class_to_file($this->appName) . '/routes.php';
		$this->routes = $routes;

		// load config
		$config = array(
			'db' => array(),
			'security' => array(
				'login' => 'login',
				'logout' => 'logout'
			)
		);
		include class_to_file($this->appName) . '/config.php';
		$this->config = $config;

		// connect to db
		if (!empty($this->config['db'])) {
			$dsn = "{$config['db']['service']}:dbname={$config['db']['db']};host={$config['db']['host']}";
			$this->pdo = new PDO($dsn, $config['db']['user'], $config['db']['password']);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		// init security if any
		if (isset($this->config['security']['class'])) {
			$this->security = new $this->config['security']['class']($this, $this->config['security']);
		}
	}

	public function run($url) {
		if (!$this->dispatch($url)) {
			echo "Error 404 - Not Found!";
		}
	}

	public function dispatch($url) {
		for ($i = 0; $i < count($this->routes); $i++) {
			$pattern = addcslashes($this->routes[$i]['pattern'], '/');
			if (preg_match("/{$pattern}/", $url, $matches)) {
				$action = new $this->routes[$i]['action']($this);
				// check user permissions
				if (isset($this->security) && !$this->security->isAllowed($action)) {
						if ($this->security->isLoggedIn()) {
							$this->redirect($this->security->config['login']);
						} else {
							$this->redirect($this->security->config['login']);
						}
						break;
					} else {
						// get exec params
						$actionParams = $action->getExecuteParams();
						$params = array();
						foreach ($actionParams as $paramName => $actionParam) {
							if (isset($matches[$paramName])) { // param was in route
								$params[$actionParam['position']] = $matches[$paramName];
							} else { // param was not in route
								if (!$actionParam['optional']) { // is not optional param
									if (DEBUG_LEVEL > 0) {
										trigger_error("Parameter '{$paramName}' for {$this->routes[$i]['action']}->execute() is not defined in route. Using null as value.", E_USER_WARNING);
									}
									$params[$actionParam['position']] = null;
								}
							}
						}

						// run action
						call_user_func_array(array($action, 'run'), $params);
						return true;
				}

			}
		}
		return false;
	}

	public function setResponse($code, $message = null, $data = null) {

	}

	public function sendError($code, $message) {
		die("Error $code: $message");
	}

}

?>