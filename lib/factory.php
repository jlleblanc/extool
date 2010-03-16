<?php
namespace Extool;

class Factory
{	
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
