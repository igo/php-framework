<?php
ob_start();

echo "<pre>";
//header('Content-type: text/plain');
//phpinfo();
require('framework/constants.php');
require('framework/functions.php');

$config = array();
$apps = array();
require('config.php');

$router = new Framework_Core_Router();
$router->route($apps);

ob_end_flush();
?>