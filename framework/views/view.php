<?php

class Framework_Views_View {
	
	public $types = array(
		'html' => array('text/html'),
		'js' => array('text/javascript', 'application/javascript', 'application/x-javascript'),
		'json' => array('application/json')
	);

	public $format = null;//'html';
	
	public $vars = array();
	
	public $app;
	
	public $action;
	
	public $viewsPath;
	
	private $helpersObj = array();
	
	public function __construct($app, $action) {
		$this->app = $app;
		$this->action = $action;
	}
	
	/**
	 * Get possible path to views.
	 * @return 
	 * @param object $class
	 */
	public static function classToViewPath($class) {
		$file = str_replace('_Actions_', '_Views_', $class);
		$file = class_to_path($file);
		$file = str_replace('_action', '', $file);
		return $file;
	}
	
	/**
	 * Renders whole page
	 * @return 
	 * @param object $layout
	 * @param object $view
	 */
	public function render($layout, $view) {
		$this->format = 'json';
		if ($this->format == null) {
			$acceptedTypes = explode(',', $_SERVER["HTTP_ACCEPT"]);
			foreach ($acceptedTypes as $acceptedType) {
				$acceptedType = trim($acceptedType);
				foreach ($this->types as $contentType => $list) {
					if (in_array($acceptedType, $list)) {
						$this->format = $contentType;
						break 2;
					}
				}
			}
			if ($this->format == null) { // unknown type, set default
				$this->format = 'html';
			}
		}
		
		$this->viewsPath = dirname($view);
		$file = $layout . ".{$this->format}.php";
		// check if this layout exists		
		if (!file_exists($file)) {
			// try using backup format
			$this->format = 'html';
		}
		
		$this->helpersObj = array(
			'helper' => new Framework_Views_Helpers_Helper(),
			'html' => new Framework_Views_Helpers_HtmlHelper()
		);

		$this->set(array('app' => $this->app));
		$this->set(array('action' => $this->action));
		$this->set($this->helpersObj);

		$blocks = $this->renderBlocks($view);
		$this->renderLayout($layout, $blocks);
	}
	
	/**
	 * Renders all content with specified format.
	 * If layout doesn't exists then try to set layout to html.
	 * If layout is not found anyway then throws exception.
	 * @return 
	 * @param object $layout
	 * @param object $blocks
	 */
	private function renderLayout($layout, $blocks) {
		$file = $layout . ".{$this->format}.php";
		if (file_exists($file)) {
			extract($blocks, EXTR_SKIP);
			extract($this->vars, EXTR_SKIP);
			include($file);
		} else {
			throw new Exception("Layout $file not found!", 500);
		}
	}
	
	/**
	 * Renders all blocks for specified view.
	 * @return array of blocks
	 * @param object $shortPath
	 */
	private function renderBlocks($shortPath) {
		$pattern = '/'.basename($shortPath)."\.([a-zA-Z0-9_-]+)\.{$this->format}\.php/";
		$files = scandir(dirname($shortPath));
		$out = array();
		foreach ($files as $file) {
			if (preg_match($pattern, $file, $matches)) {
				$out[$matches[1]] = $this->renderBlock("{$shortPath}.{$matches[1]}.{$this->format}.php");
			}
		}
		return $out;
	}
	
	/**
	 * Renders specified block and return it.
	 * @return 
	 * @param object $path
	 */
	private function renderBlock($path) {
		extract($this->vars, EXTR_SKIP);
		ob_start();
		include ($path);
		$out = ob_get_clean();
		return $out;
	}
	
	/**
	 * Sets variables which will be available during rendering.
	 * Example:
	 *  $files = array('one', 'two');
	 *  $view->set(compact('files'));
	 * @return 
	 * @param array $params
	 */
	public function set($params) {
		$this->vars += $params;
	}
	
	public function element($name, $variables = array()) {
		extract($this->helpersObj, EXTR_SKIP);
		extract($variables, EXTR_SKIP);
		include("{$this->viewsPath}/{$name}.php");
	}
	
}

?>