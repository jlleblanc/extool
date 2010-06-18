<?php
namespace Extool\Adapter;

class SimpleCSV implements \Extool\Adapter\AdapterInterface
{
	private $resource;
	private $headers = array();
	private $raw_data = array();
	private $keyed_data = array();
	private $data;

	public function decorateRepresentation(\Extool\Representation\Representation &$rep)
	{
		$this->parse();

		$pieces = explode('/', $this->resource);
		$resource_name = ucfirst(preg_replace('/\..*/', '', array_pop($pieces)));
		$rep->setName($resource_name);

		$fields = $this->determineFields();
		$data = new \Extool\Representation\Data($fields);
		$data->setData($this->keyed_data);

		$rep->addData($data, $resource_name);

		$table = new \Extool\Representation\Table($resource_name);
		$table->setFields($fields);
		$rep->addTable($table);

		$model = new \Extool\Representation\Model($resource_name);
		$model->setCriteria($fields);
		$model->addTable($table);

		$view = new \Extool\Representation\View($resource_name);
		$view->setFields($fields);
		$rep->addPublicView($view);
		$rep->addPublicModel($model);

		$view->setAccess('admin');
		$rep->addAdminView($view);
		$rep->addAdminModel($model);

		// Create new model for the list views
		$model = new \Extool\Representation\Model($resource_name . ' List');
		$model->setCriteria($fields);
		$model->addTable($table);

		$view = new \Extool\Representation\View($resource_name . ' List');
		$view->setType('list');
		$view->setFields($fields);
		$rep->addPublicView($view);
		$rep->addPublicModel($model);

		$view->setAccess('admin');
		$rep->addAdminView($view);
		$rep->addAdminModel($model);
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
			$file = fopen($this->resource, 'r');

			$this->headers = fgetcsv($file);

			while ($line = fgetcsv($file)) {
				$this->raw_data[] = $line;
				$this->keyed_data[] = $this->keyedRow($line);
			}

			fclose($file);
		}
	}

	private function keyedRow($row)
	{
		$keyed = array();

		foreach ($this->headers as $index => $header) {
			$keyed[$header] = $row[$index];
		}

		return $keyed;
	}

	private function determineFields()
	{
		$types = new \Extool\Defines\Types();
		$fields = new \Extool\Representation\Fields();

		for ($i=0, $column_count = count($this->headers); $i < $column_count; $i++) { 

			$column_data = array();

			// going into undefined offsets here, I think my array math is off
			for ($j=0, $row_count=count($this->raw_data); $j < $row_count; $j++) {
				$column_data[] = $this->raw_data[$j][$i];
			}

			$header = $this->headers[$i];

			$fields->addField($header, $types->determineTypeArray($column_data));
		}

		return $fields;
	}
}
