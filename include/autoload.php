<?php

function __autoload($classname)
{
	$segments = explode('\\', $classname);

	// remove Extool from the segments
	array_shift($segments);
	$file = implode('/', $segments);

	$path = EXTOOL_BASE . '/lib/' . $file . '.php';

	// I really don't like this logic at all. Need to find a better way of 
	// bringing individual targets and adapters into the mix.
	if (file_exists($path)) {
		require_once $path;
	}
}