<?php
namespace Extool\Defines

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
	private $valid_types = array(
		'text' => function ($value) {
			return is_string($value)
		},
		'long text' => function ($value) {
			if (strlen($value) > 255) {
				return true;
			}

			return false;
		},
		'html' => function ($value) {
			// TODO
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

	public function determineType($value)
	{
		$types = $this->determineValidTypes($value);
		return array_pop($types);
	}

	public function getValidTypes()
	{
		return array_keys($this->valid_types);
	}
}
