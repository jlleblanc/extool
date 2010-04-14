<?php

define('EXTOOL_BASE', __DIR__);

include 'include/autoload.php';

$factory = new Extool\Factory();

$adapter = $factory->getAdapter('TabTables');
$adapter->setResource('plans/vehicles');

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget('Joomla15');
	$target->setRepresentation($rep);
//	$config = $target->getConfiguration();
//	$config->author = 'Joseph LeBlanc';
//	$target->setConfiguration($config);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop/vehicles');
	$files->writeAll();
}