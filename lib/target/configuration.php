<?php
namespace Extool\Target;

class Configuration
{
	protected $fields;
	protected $values = array();
	protected $required;
	protected $types;

	public function __construct(\Extool\Representation\Fields $fields)
	{
		$this->fields = $fields;

		foreach ($this->fields as $name => $type) {
			$this->values[$name] = null;
		}

		$this->types = new \Extool\Defines\Types();
	}

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

	public function __isset($name)
	{
		if (isset($this->values[$name])) {
			return true;
		}

		return false;
	}

	public function __unset($name)
	{
		if (isset($this->values[$name])) {
			$this->values[$name] = null;
		}
	}
}
