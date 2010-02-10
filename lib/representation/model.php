<?php
namespace Extool\Representation;

/**
* 
*/
class Model
{
	protected $name;
	protected $tables;
	protected $criteria;

	function __construct($name)
	{
		$this->name = $name;
	}

	public function setCriteria(Fields $fields)
	{
		$this->criteria = $fields;
	}

	public function addTable(Table $table)
	{
		if (!isset($this->tables[$table->name])) {
			$this->tables[$table->name] = $table;
			return true;
		}

		return false;
	}

	public function __get($name)
	{
		return $this->$name;
	}
}