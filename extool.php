<?php

define('EXTOOL_BASE', __DIR__);

include 'include/autoload.php';

$factory = new Extool\Factory();

$adapter = $factory->getAdapter('TabTables');
$adapter->setResource('plans/volunteers'); // Replace with the plan you wish to use

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget('Joomla15');
	$target->setRepresentation($rep);
	$configuration = $target->getConfiguration();

	include 'configs/joe_joomla15';

	foreach ($config as $key => $value) {
		$configuration->$key = $value;
	}

	$target->setConfiguration($configuration);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop/lecomponent');
	$files->writeAll();
}