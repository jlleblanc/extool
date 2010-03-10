<?php
namespace Extool\Target;

class Joomla15 implements \Extool\Target\TargetInterface
{
	private $rep;

	public function setRepresentation(\Extool\Representation\Representation $representation)
	{
		$this->rep = $representation;
	}

	public function generate()
	{
		
	}
}
