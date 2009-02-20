<?php
define('DEBUG_LEVEL', 1);

$config['db'] = array(
	'service' => 'mysql',
	'host' => 'localhost',
	'db' => 'my_blog_app',
	'user' => 'root',
	'password' => 'P4ssw0rd'
);

$apps[] = array(
	'name' => 'MyBlogApp',
	'path' => '/'
);

$error_404_handler = 'MyBlogApp'

/*
$apps[] = array(
	name => 'CMS',
	path => '/'
);
*/
?>
