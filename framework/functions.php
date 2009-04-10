<?php

function __autoload($class) {
	$path = class_to_path("$class").'.php';
	if (file_exists($path)) {
		echo("Loading class $class from file $path\n");
		require_once $path;
	} else if (file_exists('apps/'.$path)) {
		echo("Loading class $class from file apps/$path\n");
		require_once 'apps/'.$path;
	} else {
		debug_print_backtrace();
		echo("Error loading class $class. File $path not found!\n");
	}
}

function can_import_class($class) {
	return file_exists('apps/' . class_to_path($class).'.php');
}

/**
 * Converts this_jet_text to ThisJetText
 * @return
 * @param str $s
 */
//function to_camel_case($s) {
//	return str_replace(' ', '', ucwords(str_replace('_', ' ', $s)));
//}

/**
 * Converts ThisJETText to this_jet_text
 * @return
 * @param str $s
 */
function class_to_file($s) {
	return str_replace('/_', '/', str_replace('__', '_', strtolower(preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '_$0', preg_replace('/(?!^)[[:upper:]]+/', '_$0', $s)))));
}

function class_to_path($class) {
	return class_to_file(str_replace('_', '/', $class));
}


function route($pattern, $controller, $params = array()) {
	return array(
		'pattern' => $pattern,
		'action' => $controller,
		'params' => $params
	);
}


// print_r alias
function p() {
	for ($i = 0; $i < func_num_args(); $i++)
		print_r(func_get_arg($i));
};



?>