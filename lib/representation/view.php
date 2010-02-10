<?php
namespace Extool\Representation;

/**
* 
*/
class View
{
	protected $name;
	protected $type = 'public';
	protected $key;
	protected $access;

	function __construct($name)
	{
		$this->name = $name;
	}

	public function setFields(Fields $fields)
	{
		$this->fields = $fields;
	}

	public function setType($type)
	{
		if ($type == 'public' || $type == 'private') {
			$this->type = $type;
			return true;
		}

		return false;
	}

	public function setAccess($access)
	{
		if ($access == 'public' || $access == 'admin') {
			$this->access = $access;
		}
	}

	public function __get($name)
	{
		return $this->$name;
	}
}
