<?php

function __autoload($classname)
{
	$segments = explode('\\', $classname);

	// remove Extool from the segments
	array_shift($segments);
	$file = implode('/', $segments);

	$path = 'lib/' . $file . '.php';

	// I really don't like this logic at all. Need to find a better way of 
	// bringing individual targets and adapters into the mix.
	if (file_exists($path)) {
		require_once $path;
	} else if ($segments[0] == 'Target'){
		require_once 'targets/' . array_pop($segments) . '/target.php';
	} else if ($segments[0] == 'Adapter') {
		require_once 'adapters/' . array_pop($segments) . '/adapter.php';
	}
}


require_once 'adapters/tabtables/adapter.php';

$adapter = new Extool\Adapter\TabTables();
$adapter->setResource('plans/recipes');

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = new Extool\Target\Joomla15();
	$target->setRepresentation($rep);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop');
	$files->writeAll();
}