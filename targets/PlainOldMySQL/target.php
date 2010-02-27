<?php
namespace Extool\Target;

class PlainOldMySQL implements \Extool\Target\TargetInterface
{
	private $rep;

	function getConfiguration()
	{
		
	}

	public function setConfiguration(Configuration $configuration)
	{
		
	}

	public function setRepresentation(\Extool\Representation\Representation $representation)
	{
		$this->rep = $representation;
	}

	public function generate()
	{
		echo "PUBLIC VIEWS:\n";
		foreach ($this->rep->public_views as $view) {
			echo "$view->name\n";
		}

		echo "\nPUBLIC MODELS:\n";
		foreach ($this->rep->public_models as $model) {
			echo "$model->name\n";
		}

		echo "\nADMIN VIEWS:\n";
		foreach ($this->rep->admin_views as $view) {
			echo "$view->name\n";
		}

		echo "\nADMIN MODELS:\n";
		foreach ($this->rep->admin_models as $model) {
			echo "$model->name\n";
		}

		echo "\nTABLES:\n";
		foreach ($this->rep->tables as $table) {
			echo "$table->name\n";
		}	
	}
}
