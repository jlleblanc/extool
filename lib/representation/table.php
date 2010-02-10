<?php
namespace Extool\Representation;

/**
* 
*/
class Table
{
	protected $name;
	protected $fields;

	function __construct($name)
	{
		$this->name = $name;
	}

	public function setFields(Fields $fields)
	{
		$this->fields = $fields;
	}

	public function __get($name)
	{
		if ($name == 'key') {
			if (isset($this->fields->key)) {
				return $this->fields->key;
			} else {
				throw new Exception("No Key Defined");
			}
		}

		return $this->$name;
	}
}
