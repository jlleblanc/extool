<?php

function __autoload($classname)
{
	$segments = explode('\\', $classname);

	// remove Extool from the segments
	array_shift($segments);
	$file = implode('/', $segments);

	$path = 'lib/' . $file . '.php';

	if (file_exists($path)) {
		require_once $path;
	} else if ($segments[0] == 'Target'){
		require_once 'targets/' . array_pop($segments) . '/target.php';
	}
}


require_once 'adapters/tabtables/adapter.php';

$adapter = new TabTables();
$adapter->setResource('plans/recipes');

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = new Extool\Target\PlainOldMySQL();
	$target->setRepresentation($rep);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop');
	$files->writeAll();
}