<?php

class Framework_Views_Helpers_HtmlHelper extends Framework_Views_Helpers_Helper {

	public function escape($s) {
		return htmlspecialchars($s);
	}

	public function link($title, $url) {
		echo '<a href="' . $this->url($url) . '">' . $this->escape($title) . '</a>';
	}

	public function css($url) {
		echo '<link rel="stylesheet" type="text/css" href="' . $this->url($url) . '" />';
	}

}

?>
