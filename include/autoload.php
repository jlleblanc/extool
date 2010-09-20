<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */

/*
 *  This code needs some care and attention. Anyone care to help?
 */

function __autoload($classname)
{
	$segments = explode('\\', $classname);

	// remove Extool from the segments
	array_shift($segments);
	$file = implode('/', $segments);

	$path = EXTOOL_BASE . '/lib/' . $file . '.php';

	// I really don't like this logic at all. Need to find a better way of 
	// bringing individual targets and adapters into the mix.
	if (file_exists($path)) {
		require_once $path;
	}
}