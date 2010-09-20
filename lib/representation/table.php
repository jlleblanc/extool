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
 * Represents a Table. Fields must be set for this class to be useful. The key
 * is extracted from the Fields object. A key can be created if necessary.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Table
{
	/**
	 * The system-friendly name for the table.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * An object representing the fields available in the table. If no key is
	 * set, one can be generated by createDefaultKey().
	 *
	 * @var string
	 */
	protected $fields;

	/**
	 * Requires the system-friendly name for the table.
	 *
	 * @param string $name 
	 * @author Joseph LeBlanc
	 */
	function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Used to set the fields for the table. Requires a Fields object.
	 *
	 * @param Fields $fields 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setFields(Fields $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * Checks for the presence of a key in the Fields object. If one does not
	 * already exist, a key is created using the system-friendly table name
	 * with _id appended to the end. It is created as an integer field.
	 *
	 * @return bool
	 * @author Joseph LeBlanc
	 */
	public function createDefaultKey()
	{
		// Do not create a key if one exists
		if ($this->fields->key != false) {
			return false;
		}

		$this->fields->addField($this->name . '_id', 'integer');
		if (!$this->fields->setKey($this->name . '_id')) {
			throw new \Exception("Key {$this->name}_id could not be set");
		}

		return true;
	}

	/**
	 * Allows access to all properties without allowing write access. If the 
	 * key property is requested, it is returned from the internally stored
	 * fields. If no key is assigned to the fields or the fields are not set,
	 * an exception will be thrown.
	 *
	 * @param string $name 
	 * @return mixed
	 * @author Joseph LeBlanc
	 */
	public function __get($name)
	{
		if ($name == 'key') {
			return $this->fields->key;
		} else if ($name == 'system_name') {
			return str_replace(' ', '_', strtolower($this->name));
		}

		return $this->$name;
	}
}
