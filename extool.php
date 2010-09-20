<?php
/**
 * Extool Code Generator - command line interface
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 * Command line usage:
 * 
 * php extool.php AdapterName /path/to/plan TargetName
 * 
 * This is a working example of Extool in action. For different behavior, 
 * either clone this file, or use Extool as a library.
 * 
 */
define('EXTOOL_BASE', __DIR__);

include 'include/autoload.php';

$factory = new Extool\Factory();

$adapter = $factory->getAdapter($argv[1]);
$adapter->setResource($argv[2]); // Replace with the plan you wish to use

$rep = new Extool\Representation\Representation();

$adapter->decorateRepresentation($rep);

if ($rep->validate()) {
	$target = $factory->getTarget($argv[3]);
	$target->setRepresentation($rep);
	$configuration = $target->getConfiguration();

	// pull the config file, fall back on the default config file, then finally
	// fall back on a blank configuration
	if (file_exists('targets/' . $argv[3] . '/config')) {
		include 'targets/' . $argv[3] . '/config';
	} else if (file_exists('targets/' . $argv[3] . '/config-dist')) {
		include 'targets/' . $argv[3] . '/config-dist';
	} else {
		$config = array();
	}

	foreach ($config as $key => $value) {
		$configuration->$key = $value;
	}

	$target->setConfiguration($configuration);
	$files = $target->generate();

	$product_name = preg_replace('/\..*$/', '', basename($argv[2]));
	$path = EXTOOL_BASE . '/products/' . $product_name;

	if (!file_exists($path)) {
		mkdir($path);
	}

	$files->setRoot($path);
	$files->writeAll();
}