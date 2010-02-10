<?php
namespace Extool\Representation;

/**
* 
*/
class Data
{
	protected $fields;
	protected $data;
	
	function __construct(Fields $fields)
	{
		$this->fields = $fields;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function __get($name)
	{
		return $this->$name;
	}
}
