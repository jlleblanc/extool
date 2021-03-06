<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */
namespace Extool\Defines;

/**
 * A class for defining and determining variable types.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Types
{
	/**
	 * An array of functions that validate data types, keyed by the data type
	 * names.
	 *
	 * @var array
	 */
	private $valid_types;

	/**
	 * Annoyingly, the class properties can't take an array of functions as a
	 * default value. This constructor sets them.
	 *
	 * @author Joseph LeBlanc
	 */
	public function __construct()
	{
		$this->valid_types = array(
			'text' => function ($value) {
				return is_string($value);
			},
			'long text' => function ($value) {
				if (strlen($value) > 255) {
					return true;
				}

				return false;
			},
			'html' => function ($value) {
				if (strlen($value) > strlen(strip_tags($value))) {
					return true;
				}

				return false;
			},
			'url'  => function ($value) {
				return filter_var('example.com', FILTER_VALIDATE_URL);
			},
			'email'  => function ($value) {
				return filter_var($value, FILTER_VALIDATE_EMAIL);
			},
			'integer' => function ($value) {
				return filter_var($value, FILTER_VALIDATE_INT);
			},
			'decimal' => function ($value) {
				return filter_var($value, FILTER_VALIDATE_FLOAT);
			},
			'money' => function ($value) {
				// TODO
				return false;
			},
			'date' => function ($value) {
				// TODO
				return false;
			},
			'color_hex' => function ($value) {
				if (preg_match('/#[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?/', $value)) {
					return true;
				}

				return false;
			}
		);
	}

	/**
	 * Returns an array of all the possible types that $value validates.
	 *
	 * @param mixed $value 
	 * @return array
	 * @author Joseph LeBlanc
	 */
	public function determineValidTypes($value)
	{
		$types = array();

		foreach ($this->valid_types as $key => $func) {
			if($func($value)) {
				$types[] = $key;
			}
		}

		return $types;
	}

	/**
	 * Given a value, this function returns the most specific type that the
	 * value validates.
	 *
	 * @param mixed $value 
	 * @return string
	 * @author Joseph LeBlanc
	 */
	public function determineType($value)
	{
		$types = $this->determineValidTypes($value);
		return array_pop($types);
	}

	/**
	 * Given an array, this function returns the most specific type that the
	 * values in the array validate.
	 *
	 * @param string $values 
	 * @return string
	 * @author Joseph LeBlanc
	 */
	public function determineTypeArray($values)
	{
		$valid = array_keys($this->valid_types);
		$types = array();

		foreach ($values as $val) {
			$types[] = $this->determineType($val);
		}

		$valid = array_intersect($valid, $types);

		return array_pop($valid);
	}

	/**
	 * Returns an array of all valid value types.
	 *
	 * @return array
	 * @author Joseph LeBlanc
	 */
	public function getValidTypes()
	{
		return array_keys($this->valid_types);
	}

	/**
	 * Returns true if the data given matches the type specified.
	 *
	 * @param mixed $data 
	 * @param string $type 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function validateData($data, $type)
	{
		return call_user_func($this->valid_types[$type], $data);
	}
}
