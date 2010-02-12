<?php
namespace Extool\Representation;

/**
 * This class is designed for filtering and containing a dataset. A Fields
 * object is required for construction and is later used to filter the data
 * that gets passed in.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Data
{
	/**
	 * An object representing the fields the dataset is filtered against.
	 *
	 * @var string
	 */
	protected $fields;

	/**
	 * An array of the data, keyed by human-friendly key names.
	 *
	 * @var string
	 */
	protected $data;

	/**
	 * Requires a Fields object defining the valid fields for the dataset.
	 *
	 * @param Fields $fields 
	 * @author Joseph LeBlanc
	 */
	function __construct(Fields $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * Accepts an array of data, keyed by human-friendly field names. Any 
	 * fields present in this array but not present in the internal $fields
	 * variable will be ignored.
	 *
	 * @param array $data 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setData($data)
	{
		$this->data = array();

		foreach ($data as $row) {
			$filtered_row = array();

			foreach ($row as $key => $value) {
				if (isset($this->fields->names[$key])) {
					$filtered_row[$key] = $value;
				}
			}

			if (count($filtered_row)) {
				$this->data[] = $filtered_row;
			}
		}
	}

	/**
	 * Allows the contents of another data object to be merged with the
	 * current one. Both Data objects must have the same set of fields.
	 *
	 * @param Data $data 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function mergeIn(Data $data)
	{
		if ($data->fields == $this->fields) {
			$this->data = array_merge($this->data, $data->data);
		} else {
			throw new Exception("Data types do not match");
		}
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
