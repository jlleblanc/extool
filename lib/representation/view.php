<?php
namespace Extool\Representation;

/**
 * Represents a single View. The view can either be intended to display a
 * single record, or a set of records in a list. The view can be designated
 * as for admins only, or one that's publicly available.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class View
{
	/**
	 * The human-friendly name for the view.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The type of view; can either be a list to show multiple records, or a
	 * view showing a single record.
	 *
	 * @var string
	 */
	protected $type = 'single';

	/**
	 * An object representing the fields displayed in the view.
	 *
	 * @var string
	 */
	protected $fields;

	/**
	 * Designates the access level of the view. Can either be admin or public.
	 *
	 * @var string
	 */
	protected $access = 'public';

	/**
	 * Requires the human-friendly name for the view.
	 *
	 * @param string $name 
	 * @author Joseph LeBlanc
	 */
	function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Used to set the fields for the view. Requires a Fields object.
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
	 * Sets the View type. The value must either be single or list
	 *
	 * @param string $type 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function setType($type)
	{
		if ($type == 'single' || $type == 'list') {
			$this->type = $type;
			return true;
		}

		return false;
	}

	/**
	 * Sets the access level for the view. The value must either be public or
	 * admin.
	 *
	 * @param string $access 
	 * @return bool
	 * @author Joseph LeBlanc
	 */
	public function setAccess($access)
	{
		if ($access == 'public' || $access == 'admin') {
			$this->access = $access;
			return true;
		}

		return false;
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
			if (isset($this->fields->key)) {
				return $this->fields->key;
			} else {
				throw new \Exception("No Key Defined");
			}
		} else if ($name == 'system_name') {
			return str_replace(' ', '_', strtolower($this->name));
		}

		return $this->$name;
	}
}
