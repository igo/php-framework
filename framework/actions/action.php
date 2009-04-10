<?php

class Framework_Actions_Action {

	public $app;

	public $layout = 'default';

	public $viewClass = 'Framework_Views_View';

	public function __construct($app) {
		$this->app = $app;
	}

	public function execute() {

	}

	public function run() {
		// create view
		$this->view = new $this->viewClass($this->app, $this);

		// run action
		$arguments = func_get_args();
		call_user_func_array(array($this, 'execute'), $arguments);

		// render
		$file = Framework_Views_View::classToViewPath(get_class($this));
		$layout = class_to_file($this->app->appName) . "/layouts/{$this->layout}";
		$this->view->render($layout, $file);
	}

	public function getExecuteParams() {
		$class = get_class($this);
		$reflectionClass = new ReflectionClass($class);
		$reflectionParams = $reflectionClass->getMethod('execute')->getParameters();
		$params = array();
		foreach ($reflectionParams as $reflectionParam) {
			$params[$reflectionParam->getName()] = array(
				'optional' => $reflectionParam->isOptional(),
				'position' => $reflectionParam->getPosition()
			);
		}
		return $params;
	}

	public function getModel($name) {
		return $this->app->getModel($name);
	}

	public function redirect($url) {
		$this->app->redirect($url);
	}

	public function sendError($code, $message) {
		$this->app->sendError($code, $message);
	}

}

?>
