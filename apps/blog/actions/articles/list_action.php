<?php

class MyBlogApp_Actions_Articles_ListAction extends Framework_Actions_Action {
	
	public function execute() {
		$articles = $this->getModel('Article')->fetchAll();
		$this->view->set(compact('articles'));
	}
	
}

?>
