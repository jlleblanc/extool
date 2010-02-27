<?php
namespace Extool\Representation;

/**
 * Represents a set of fields, keeping track of human friendly labels and 
 * field types.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Fields
{
	/**
	 * System friendly names, keyed by human friendly label
	 *
	 * @var array
	 */
	protected $names = array();
	
	/**
	 * Field types, keyed by human friendly label
	 *
	 * @var string
	 */
	protected $types = array();

	/**
	 * The key variable for the set of fields. Can be left false, but necessary
	 * for use in the context of tables and views.
	 *
	 * @var string
	 */
	protected $key = false;

	/**
	 * Accepts an array of field types, keyed by human friendly label
	 *
	 * @param array $labels 
	 * @author Joseph LeBlanc
	 */
	public function __construct($fields = array())
	{
		if (count($fields)) {
			$this->addFieldsFromArray($fields);
		}
	}

	/**
	 * Adds a field to the fieldset, given the human friendly label and field
	 * type.
	 *
	 * @param string $label 
	 * @param string $type 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function addField($label, $type)
	{
		if (!isset($this->names[$label])) {
			$this->names[$label] = $this->getSystemName($label);
			$this->types[$label] = $type;
			return true;
		}

		return false;
	}

	/**
	 * Sets the key for the fields. The key specified in $name must already
	 * be present in the fieldset.
	 *
	 * @param string $name 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function setKey($name)
	{
		if (in_array($name, $this->names)) {
			$this->key = $name;
			return true;
		}

		return false;
	}

	/**
	 * Sets multiple fields, given an array of field types, keyed by human
	 * friendly labels.
	 *
	 * @param array $fields 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addFieldsFromArray(array $fields)
	{
		foreach ($fields as $label => $type) {
			$this->addField($label, $type);
		}
	}

	/**
	 * Removes a field, given the human friendly label
	 *
	 * @param string $label 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function removeField($label)
	{
		if (isset($this->names[$label])) {
			unset($this->names[$label]);
			unset($this->types[$label]);
			return true;
		}

		return false;
	}

	/**
	 * Takes a human friendly label and returns system name version
	 *
	 * @param string $label 
	 * @return string
	 * @author Joseph LeBlanc
	 */
	public function getSystemName($label)
	{
		$name = str_replace(' ', '_', $label);
		return preg_replace('/[^A-Za-z_0-9]/', '', $name);
	}

	/**
	 * Allows access to names and types without being able to set them
	 *
	 * @param string $name 
	 * @return mixed
	 * @author Joseph LeBlanc
	 */
	public function __get($name)
	{
		return $this->$name;
	}
}
