<?php
namespace Extool\Representation;

/**
* 
*/
class Table
{
	protected $name;
	protected $fields;
	protected $key;

	function __construct($name)
	{
		$this->name = $name;
		$this->key = $name . '_id';
	}

	public function setFields(Fields $fields)
	{
		$this->fields = $fields;

		// TODO: need to find a better way of defining the key
		foreach ($fields as $title => $type) {
			if ($type == 'key') {
				$this->key = $title;
			}
		}
	}

	public function __get($name)
	{
		return $this->$name;
	}
}
