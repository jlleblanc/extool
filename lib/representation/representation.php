<?php
namespace Extool\Representation;

/**
 * This class holds a representation of a complete extension to be generated
 * by Extool. It serves as a composite object that holds all of the views,
 * models, tables, and data necessary to generate the extension.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Representation
{
	// These objects are requried as a bare minimum. If any of these are left
	// unset, the validate() function will return false.

	/**
	 * View objects intended to be used for generating public facing views.
	 *
	 * @var array
	 */
	protected $public_views;

	/**
	 * Model objects matching the public view objects
	 *
	 * @var array
	 */
	protected $public_models;

	/**
	 * Table objects for the extension as a whole.
	 *
	 * @var array
	 */
	protected $tables;


	// These objects are optional.

	/**
	 * View objects intended to be used for generating admin facing views.
	 *
	 * @var array
	 */
	protected $admin_views;

	/**
	 * Model objects matching the admin view objects
	 *
	 * @var array
	 */
	protected $admin_models;

	/**
	 * Data the extension should be prefilled with on installation.
	 *
	 * @var string
	 */
	protected $data;

	/**
	 * Determines whether the object has all of the properties set necessary
	 * to complete it.
	 *
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	public function validate()
	{
		if (!isset($public_views) || !isset($tables) || !isset($public_models)) {
			return false;
		}

		return true;
	}
}
