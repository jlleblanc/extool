<?php

class Joomla15Controller
{
	private $component;
	private $admin;
	private $table;
	private $defaultView;
	
	function __construct($defaultView = '', $admin = false, $table = '')
	{
		$this->admin = $admin;
		$this->table = $table;
		$this->defaultView = str_replace(array(' ', '_'), '', strtolower($defaultView));
		
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		$this->component = $plan->project;
	}
	
	public function makeControllerCode()
	{
		$functions = $this->getFunctions();
		
		$controlSnip = new Snippet('controller');
		$controlSnip->assign('{{component}}', ucfirst($this->component));
		$controlSnip->assign('{{functions}}', $functions);
		$controlSnip->assign('{{default_view}}', $this->defaultView);
		
		if ($this->admin) {
			$controlSnip->assign('{{controller_name}}', ucfirst($this->defaultView));
		} else {
			$controlSnip->assign('{{controller_name}}', '');
		}
		
		return $controlSnip;
	}
	
	private function getFunctions()
	{
		if (!$this->admin) {
			return '';
		}
		
		$funSnip = new Snippet('controller_functions');
		$funSnip->assign('{{component}}', $this->component);
		
		if ($this->component == $this->table) {
			$table = $this->table;
		} else {
			$table = $this->component . '_' . $this->table;
		}
		
		$funSnip->assign('{{table}}', $table);
		$funSnip->assign('{{tablename}}', ucfirst(str_replace('_', ' ', $this->table)));
		$funSnip->assign('{{controller_name}}', $this->defaultView);
		
		$addView = preg_replace('/_?list/i', '', $this->defaultView);
		$funSnip->assign('{{add_view}}', $addView);
		
		return $funSnip;
	}
}