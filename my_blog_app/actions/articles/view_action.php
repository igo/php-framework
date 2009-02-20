<?php

class MyBlogApp_Actions_Articles_ViewAction extends Framework_Actions_Action {
	
	public function execute($id) {
		$article = $this->
			getModel('Article')->
			filter('id', '=', $id)->
			fetch();
		//$this->existsOr404($article);
		$this->view->set(compact('article'));
	}
	
}

?>
