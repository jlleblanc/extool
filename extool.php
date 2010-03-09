<?php

function __autoload($classname)
{
	$segments = explode('\\', $classname);

	// remove Extool from the segments
	array_shift($segments);
	$file = implode('/', $segments);
	require_once 'lib/' . $file . '.php';
}


require_once 'adapters/tabtables/adapter.php';

$adapter = new TabTables();
$adapter->setResource('plans/recipes');

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	require_once 'targets/PlainOldMySQL/target.php';
	$target = new Extool\Target\PlainOldMySQL();
	$target->setRepresentation($rep);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop');
	$files->writeAll();
}