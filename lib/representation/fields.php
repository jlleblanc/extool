<?php
namespace Extool\Representation;

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
	
	public function __construct()
	{
		
	}

	public function setField($label, $type)
	{
		if (!isset($this->names[$label])) {
			$this->names[$label] = $this->getSystemName($label);
			$this->types[$label] = $type;
			return true;
		}

		return false;
	}

	public function removeField()
	{
		if (isset($this->names[$label])) {
			unset($this->names[$label]);
			unset($this->types[$label]);
			return true;
		}

		return false;
	}

	private function getSystemName($label)
	{
		$name = str_replace(' ', '_', $label);
		return preg_replace('/[^A-Za-z_0-9]/', '', $name);
	}

	public function __get($name)
	{
		return $this->$name;
	}
}
