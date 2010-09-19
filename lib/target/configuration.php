<?php
namespace Extool\Target;

/**
 * A class designed to hold and validate the configuration for a Target
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Configuration
{
	/**
	 * An Fields object containing the required fields for the configuration.
	 *
	 * @var \Extool\Representation\Fields
	 */
	protected $fields;

	/**
	 * A keyed array of values for the configuration. This is kept separate 
	 * from the Fields object, which is used for validation.
	 *
	 * @var array
	 */
	protected $values = array();

	/**
	 * A Types object used to validate data against the types defined in the
	 * $fields object.
	 *
	 * @var \Extool\Defines\Types
	 */
	protected $types;

	/**
	 * Requires a Fields object defining the required fields for the
	 * configuration.
	 *
	 * @param Fields $fields 
	 * @author Joseph LeBlanc
	 */
	public function __construct(\Extool\Representation\Fields $fields)
	{
		$this->fields = $fields;

		foreach ($this->fields as $name => $type) {
			$this->values[$name] = null;
		}

		$this->types = new \Extool\Defines\Types();
	}

	/**
	 * Magic PHP method that validates configuration values against $fields
	 * object before setting values
	 *
	 * @param string $name 
	 * @param mixed $value 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function __set($name, $value)
	{
		if (isset($this->fields[$name])) {
			if ($this->types->validateData($value, $this->fields[$name])) {
				$this->values[$name] = $value;
			} else {
				throw new \Exception("Invalid data for type " . $this->values[$name]);
			}
			
		} else {
			throw new \Exception("Attempting to set undefined field in Configuration");
		}
	}

	/**
	 * Magic PHP method that only allows retrieval of configuration values that
	 * actually exist.
	 *
	 * @param string $name 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function __get($name)
	{
		if ($name == 'fields') {
			return $this->fields;
		} else if (isset($this->fields[$name])) {
			return $this->values[$name];
		} else {
			throw new \Exception("Attempting to get undefined field from configuration");
		}
	}

	/**
	 * Magic PHP method for testing whether or not a certain configuration
	 * field exists
	 *
	 * @param string $name 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function __isset($name)
	{
		if (isset($this->values[$name])) {
			return true;
		}

		return false;
	}

	/**
	 * Magic PHP method for unsetting configuration fields, if they exist
	 *
	 * @param string $name 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function __unset($name)
	{
		if (isset($this->values[$name])) {
			$this->values[$name] = null;
		}
	}
}
