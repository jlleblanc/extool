<?php

define('EXTOOL_BASE', __DIR__);

include 'include/autoload.php';

$factory = new Extool\Factory();

$adapter = $factory->getAdapter('AddTheAdapterNameHere');
$adapter->setResource('plans/recipes'); // Replace with the plan you wish to use

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget('AddTheTargetNameHere');
	$target->setRepresentation($rep);
	$files = $target->generate();

	$files->setRoot('/add/your/base/path/here');
	$files->writeAll();
}