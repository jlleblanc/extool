<?php
namespace Extool\Adapter;

class TabTables implements \Extool\Adapter\AdapterInterface
{
	private $resource;
	private $table_fields = array();

	public function decorateRepresentation(\Extool\Representation\Representation &$rep)
	{
		$this->parse();

		$pieces = explode('/', $this->resource);
		$rep->setName(array_pop($pieces));

		foreach ($this->table_fields as $table_name => $fields) {
			$table = new \Extool\Representation\Table($table_name);
			$table->setFields($fields);
			$rep->addTable($table);			

			$model = new \Extool\Representation\Model($table_name);
			$model->setCriteria($fields);
			$model->addTable($table);

			// Generate four views for each table
			$view = new \Extool\Representation\View($table_name);
			$view->setFields($fields);
			$rep->addPublicView($view);
			$rep->addPublicModel($model);

			$view->setAccess('admin');
			$rep->addAdminView($view);
			$rep->addAdminModel($model);

			// Create new model for the list views
			$model = new \Extool\Representation\Model($table_name . ' List');
			$model->setCriteria($fields);
			$model->addTable($table);

			$view = new \Extool\Representation\View($table_name . ' List');
			$view->setType('list');
			$view->setFields($fields);
			$rep->addPublicView($view);
			$rep->addPublicModel($model);

			$view->setAccess('admin');
			$rep->addAdminView($view);
			$rep->addAdminModel($model);			
		}
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
		if (isset($this->resource)) {
			$contents = file_get_contents($this->resource);
			$this->setTablesFromString($contents);
		} else {
			throw new \Exception("Resource file not set");
		}
	}

	private function setTablesFromString($tables_file)
	{
		$lines = explode("\n", $tables_file);

		$current_table = '';
		foreach ($lines as $line) {
			if (substr_count($line, "\t")) {
				$field_name = trim($line);

				$type = 'text';

				if (preg_match('/[_ ]id$/Ui', $field_name)) {
					$type = 'integer';
				} 

				$this->table_fields[$current_table]->addField($field_name, $type);
			} else if ($line != "") {
				$current_table = trim($line);
				$this->table_fields[$current_table] = new \Extool\Representation\Fields();
			}
		}
	}
}
