<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */
namespace Extool\Representation;

/**
 * Represents a Model. Models require both criteria (a set of fields) and
 * tables to be fully functional.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Model
{
	/**
	 * The system-friendly name of the model
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * An array of Table objects, keyed by table name
	 *
	 * @var array
	 */
	protected $tables;

	/**
	 * A Fields object containing the fields the model should retrieve.
	 *
	 * @var \Extool\Representation\Fields
	 */
	protected $criteria;

	/**
	 * Requires the system-friendly name for the model.
	 *
	 * @param string $name 
	 * @author Joseph LeBlanc
	 */
	function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Used to set the fields to be returned by the model. Requires a Fields 
	 * object.
	 *
	 * @param Fields $fields 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setCriteria(Fields $fields)
	{
		$this->criteria = $fields;
	}

	/**
	 * Used to add tables to the model. Will only add the table if one with 
	 * the same name has not already been added.
	 *
	 * @param Table $table 
	 * @return bool
	 * @author Joseph LeBlanc
	 */
	public function addTable(Table $table)
	{
		if (!isset($this->tables[$table->name])) {
			$this->tables[$table->name] = $table;
			return true;
		}

		return false;
	}

	/**
	 * Returns the status of the model. If tables or criteria are not set, 
	 * validation fails and false is returned.
	 *
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function validate()
	{
		if (isset($this->tables) || isset($this->criteria)) {
			return true;
		}

		return false;
	}

	/**
	 * Allows access to all properties without allowing write access.
	 *
	 * @param string $name 
	 * @return mixed
	 * @author Joseph LeBlanc
	 */
	public function __get($name)
	{
		if ($name == 'system_name') {
			return str_replace(' ', '_', strtolower($this->name));
		}

		return $this->$name;
	}
}