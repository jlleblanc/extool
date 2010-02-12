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

	/**
	 * Adds a public view, if it is not already present. The access of the view
	 * is enforced by this function.
	 *
	 * @param View $view 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addPublicView(View $view)
	{
		if (isset($view->access != 'public')) {
			throw new Exception("View {$view->name} is not designated for 'public' access.");
		} else if (isset($this->public_views[$view->name])) {
			throw new Exception("Public View {$view->name} is already present in this representation");
		}

		$this->public_views[$view->name] = $view;
	}

	/**
	 * Adds a public model when a corresponding public view is available and
	 * when the public model has not already been added.
	 *
	 * @param Model $model 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addPublicModel(Model $model)
	{
		if (isset($this->public_views[$model->name])) {
			if (isset($this->public_models[$model->name])) {
				throw new Exception("Public Model {$model->name} is already present in this representation");
			}

			$this->public_models[$model->name] = $model;
		}

		throw new Exception("A matching public view for Model {$model->name} was not found");
	}

	/**
	 * Adds a Table to the representation, if it is not already present.
	 *
	 * @param Table $table 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addTable(Table $table)
	{
		if (isset($this->tables[$table->name])) {
			throw new Exception("Table {$table->name} is already present in this representation");
		}

		$this->tables[$table->name] = $table;
	}

	/**
	 * Adds an admin view, if it is not already present. The access of the view
	 * is enforced by this function.
	 *
	 * @param View $view 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addAdminView(View $view)
	{
		if (isset($view->access != 'admin')) {
			throw new Exception("View {$view->name} is not designated for 'admin' access.");
		} else if (isset($this->admin_views[$view->name])) {
			throw new Exception("Admin View {$view->name} is already present in this representation");
		}

		$this->admin_views[$view->name] = $view;
	}

	/**
	 * Adds an admin model when a corresponding admin view is available and
	 * when the admin model has not already been added.
	 *
	 * @param Model $model 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addAdminModel(Model $model)
	{
		if (isset($this->admin_views[$model->name])) {
			if (isset($this->admin_views[$model->name])) {
				throw new Exception("Admin Model {$model->name} is already present in this representation");
			}

			$this->admin_models[$model->name] = $model;
		}

		throw new Exception("A matching admin view for Model {$model->name} was not found");
	}

	/**
	 * Adds a data set (represented by a Data object) to the representation. 
	 * If the representation already has a dataset, a merge is attempted.
	 *
	 * @param Data $data 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addData(Data $data)
	{
		if (isset($this->data)) {
			$this->data->mergeIn($data);
		} else {
			$this->data = $data;
		}
	}
}
