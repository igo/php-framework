<?php

abstract class Framework_App_Security_GenericSecurity {
	
	protected $app;
	public $config;
	
	public function __construct(Framework_App_Application $app, array $config) {
		$this->app = $app;
		$this->config = $config;
	}
		
	public abstract function init();	
	
	public abstract function deinit();
	
	public abstract function isAllowed($action);
	
	public function isLoggedIn() {
		return !empty($this->user());
	}
	
	public abstract function user();
	
}

?>