<?php
define('DEBUG_LEVEL', 1);

$config['db'] = array(
	'service' => 'mysql',
	'host' => 'localhost',
	'db' => 'database',
	'user' => 'root',
	'password' => 'P4ssw0rd'
);

$apps[] = array(
	'name' => 'Blog',
	'path' => '/'
);

$error_404_handler = 'Blog'

/*
$apps[] = array(
	name => 'CMS',
	path => '/'
);
*/
?>