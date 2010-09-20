<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */
namespace Extool\Adapter;

class NapkinSketch implements AdapterInterface
{
	private $resource;
	private $raw_plan;
	private $parsed;
	private $views;
	private $adminViews;
	private $models;
	private $adminModels;
	private $tables;

	public function decorateRepresentation(\Extool\Representation\Representation &$rep)
	{
		$this->parse();

		if (isset($this->parsed['project'])) {
			$rep->setName($this->parsed['project']);
		} else {
			throw new Exception("Plan is not in NapkinSketch format.");
		}

		$tables = $this->getTables();

		foreach ($tables as $table) {
			$rep->addTable($table);
		}

		foreach ($this->views as $view) {
			$rep->addPublicView($view);
		}

		foreach ($this->adminViews as $view) {
			$rep->addAdminView($view);
		}

		foreach ($this->models as $model) {
			$rep->addPublicModel($model);
		}

		foreach ($this->adminModels as $model) {
			$rep->addAdminModel($model);
		}

		// TODO: models, views
	}

	public function setResource($resource)
	{
		if(is_file($resource)) {
			$this->resource = $resource;
		} else {
			throw new \Exception("File does not exist");
		}
	}

	public function parse()
	{
		$this->raw_plan = file_get_contents($this->resource);

		preg_match_all('/^(\w+):\s+(.+)/m', $this->raw_plan, $matches);

		$properties = $this->getMatchedProperties($matches);

		foreach ($properties as $key => $value) {
			$this->parsed[$key] = $value;
		}

		$this->views = $this->parseViews();
		$this->adminViews = $this->parseViews('admin');		
	}

	private function getTables()
	{
		if (!isset($this->tables)) {
			$this->tables = array();

			preg_match_all('/table\s+(\w+)\s*\((.+)\)/sU', $this->raw_plan, $matches);

			foreach ($matches[1] as $i => $table) {
				$fields = trim($matches[2][$i]);
				$fields = explode("\n", $fields);

				$extool_fields = new \Extool\Representation\Fields();

				foreach ($fields as $field) {
					$title = $this->makeFieldTitle($field);
					$type = $this->getFieldType($field);

					if ($type == 'keyint') {
						$extool_fields->addField($title, 'integer');
						$extool_fields->setKey($title);
					} else {
						$extool_fields->addField($title, $type);
					}
				}

				$table = new \Extool\Representation\Table($table);
				$table->setFields($extool_fields);
				$this->tables[$table->name] = $table;
			}
		}

		return $this->tables;
	}

	private function parseViews($prefix = '')
	{
		$views = array();

		preg_match_all('/^' . $prefix . 'view\s+(.+)\s*\((.+)\)/sUm', $this->raw_plan, $matches);

		foreach ($matches[1] as $i => $view_name) {
			preg_match_all('/(\w+):\s+(.+)/', $matches[2][$i], $fields);

			$tables = $this->getMatchedProperties($fields, true);

			$model = $this->makeModel($tables, $view_name, $prefix);

			$merged_fields = new \Extool\Representation\Fields();

			foreach ($model->tables as $table) {
				foreach ($table->fields as $field_name => $type) {
					$merged_fields->addField($field_name, $type);
				}
			}

			$view = new \Extool\Representation\View($view_name);

			if ($prefix == 'admin') {
				$view->setAccess('admin');
			}

			if (preg_match('/ list$/i', $view_name)) {
				$view->setType('list');
			}

			$view->setFields($merged_fields);
			$views[$view_name] = $view;
		}

		return $views;
	}

	private function makeModel($view_tables, $view, $prefix = '')
	{
		$tables = $this->getTables();

		$model = new \Extool\Representation\Model($view);

		// An extremely ugly way of creating filtered fields for models
		foreach ($view_tables as $table_name => $table) {
			if ($table[0] = '*') {
				$model->addTable($tables[$table_name]);
			} else {
				$fields = new \Extool\Representation\Fields();

				foreach ($table as $field) {
					$fields->addField($field, $tables[$table_name]->types[$field]);
				}

				$model_table = new \Extool\Representation\Table($table_name);
				$model->addTable($model_table);
			}
		}

		if ($prefix == 'admin') {
			$this->adminModels[$view] = $model;
		} else {
			$this->models[$view] = $model;
		}

		return $model;
	}

	private function getMatchedProperties($matches, $returnArray = false)
	{
		$data = array();

		foreach ($matches[1] as $i => $key) {

			$value = $matches[2][$i];

			if (stristr($value, ',')) {
				$value = explode(',', $value);

				// This removes all of the extra whitespace
				// there should be a more succinct way of writing this :(
				foreach ($value as $i => $val) {
					$value[$i] = trim($val);
				}
			} else if ($returnArray) {
				$value = array($value);
			}

			$data[$key] = $value;
		}

		return $data;
	}

	private function makeFieldTitle($field)
	{
		return preg_replace('/ @(.*)/', '', $field);
	}

	private function getFieldType($field)
	{
		// default: use plain text field
		$type = 'text';

		if ($field == 'id') {
			$type = 'keyint';
		} else if (preg_match('/(_id|_num|@int)$/', $field) || $field == 'ordering') {
			$type = 'integer';
		} else if (preg_match('/(_date|@date)$/', $field)) {
			$type = 'date';
		} else if (preg_match('/(_text|@text)$/', $field)) {
			$type = 'long text';
		} else if ($field == 'published') {
			$type = 'integer';
		}
	
		return $type;
	}
}
