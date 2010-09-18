<?php
/**
 * Extool code generator
 * 
 * Command line usage:
 * 
 * php extool.php AdapterName /path/to/plan /path/to/config
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
	$target = $factory->getTarget('Joomla15');
	$target->setRepresentation($rep);
	$configuration = $target->getConfiguration();

	include $argv[3];

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