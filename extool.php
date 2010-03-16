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
	}
}

$factory = new Extool\Factory();

$adapter = $factory->getAdapter('TabTables');
$adapter->setResource('plans/recipes');

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget('Joomla15');
	$target->setRepresentation($rep);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop');
	$files->writeAll();
}