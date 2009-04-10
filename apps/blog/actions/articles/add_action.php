<?php

class Blog_Actions_Articles_AddAction extends Framework_Actions_Action {

	public function execute() {
		if (isset($_POST['article'])) {
			$result = $this->
				getModel('Article')->
				multiSet($_POST['article'])->
				insert();
			p($result);
			if ($result['status'] == 'ok') {
				$this->redirect('../');
			} else {

			}
		}
	}

}

?>