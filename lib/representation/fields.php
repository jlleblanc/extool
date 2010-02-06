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
	 * Accepts an array of field types, keyed by human friendly label
	 *
	 * @param array $labels 
	 * @author Joseph LeBlanc
	 */
	public function __construct($fields = array())
	{
		if (count($fields)) {
			foreach ($fields as $label => $type) {
				$this->setField($label, $type)
			}
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
	public function setField($label, $type)
	{
		if (!isset($this->names[$label])) {
			$this->names[$label] = $this->getSystemName($label);
			$this->types[$label] = $type;
			return true;
		}

		return false;
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
	protected function getSystemName($label)
	{
		$name = str_replace(' ', '_', $label);
		return preg_replace('/[^A-Za-z_0-9]/', '', $name);
	}

	/**
	 * Allows us to get the names and types without being able to set them
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
