<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */
namespace Extool;

/**
 * This class is a Factory for Extool objects. 
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Factory
{
	/**
	 * Given the name of an adapter in the 'adapters' folder, this function
	 * loads the adapter class file, then returns a new object for that 
	 * adapter.
	 *
	 * @param string $name 
	 * @return object
	 * @author Joseph LeBlanc
	 */
	public function getAdapter($name)
	{
		$path = 'adapters/' . $name . '/adapter.php';
		if (is_file($path)) {
			require_once $path;
			$class = "Extool\\Adapter\\{$name}";
			return new $class();
		} else {
			throw new \Exception("Path to the {$name} adapter does not exist at {$path}");
		}
	}

	/**
	 * Given the name of a target in the 'targets' folder, this function
	 * loads the target class file, then returns a new object for that target.
	 *
	 * @param string $name 
	 * @return object
	 * @author Joseph LeBlanc
	 */
	public function getTarget($name)
	{
		$path = 'targets/' . $name . '/target.php';
		if (is_file($path)) {
			require_once $path;
			$class = "Extool\\Target\\{$name}";
			return new $class();
		} else {
			throw new \Exception("Path to the {$name} target does not exist at {$path}");
		}		
	}
}
