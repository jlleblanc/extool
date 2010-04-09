<?php
namespace Extool\Target;

abstract class Configuration
{
	protected $fields;
	protected $data;
	protected $required;

	function __construct()
	{
		
	}

	abstract function setFields();

	public function setField($name, $value)
	{
		
	}
}
