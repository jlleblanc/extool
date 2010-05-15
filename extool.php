<?php

define('EXTOOL_BASE', __DIR__);

include 'include/autoload.php';

$factory = new Extool\Factory();

$adapter = $factory->getAdapter('TabTables');
$adapter->setResource('plans/recipes'); // Replace with the plan you wish to use

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget('Joomla15');
	$target->setRepresentation($rep);
	$config = $target->getConfiguration();
	$config->author = 'Joseph LeBlanc';
	$config->project = 'recipes';
	$config->name = 'Recipes';
	$config->license = 'GPL';
	$config->version = '1.0';
	$config->description = '';
	$config->email = 'contact@jlleblanc.com';

	$target->setConfiguration($config);
	$files = $target->generate();

	$files->setRoot('/Users/josephleblanc/Desktop/lecomponent');
	$files->writeAll();
}