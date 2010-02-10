<?php
namespace Extool\Representation;

/**
* 
*/
class Representation
{
	public $admin_views;
	public $public_views;
	public $tables;
	public $data;
	public $admin_models;
	public $public_models;
	
	function __construct()
	{
		
	}

	public function validate()
	{
		if (!isset($public_views) || !isset($tables) || !isset($public_models)) {
			return false;
		}

		return true;
	}
}
