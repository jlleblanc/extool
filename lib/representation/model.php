<?php
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
	protected $name;
	protected $tables;
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