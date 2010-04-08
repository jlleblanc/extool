<?php

define('EXTOOL_BASE', __DIR__);

include 'include/autoload.php';

$factory = new Extool\Factory();

$adapter = $factory->getAdapter('TabTables');
$adapter->setResource('plans/recipes');

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget('Joomla15');
	$target->setRepresentation($rep);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop/lecomponent');
	$files->writeAll();
}