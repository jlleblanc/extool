<?php
namespace Extool\Target;

class Joomla15Controller
{
	private $component;
	private $admin;
	private $table;
	private $defaultView;

	private $rep;
	private $snippets;

	function __construct($rep, $defaultView, $admin = false, $table = '')
	{
		$this->admin = $admin;
		$this->table = $table;
		$this->defaultView = $defaultView->system_name;

		$this->component = $rep->name;

		$this->snippets = new \Extool\Helpers\SnippetFactory();
		$this->snippets->setBasePath('targets/Joomla15/snip');
	}
	
	public function makeControllerCode()
	{
		$functions = $this->getFunctions();
		
		$controlSnip = $this->snippets->getSnippet('controller');
		$controlSnip->assign('component', ucfirst($this->component));
		$controlSnip->assign('functions', $functions);
		$controlSnip->assign('default_view', $this->defaultView);
		
		if ($this->admin) {
			$controlSnip->assign('controller_name', ucfirst($this->defaultView));
		} else {
			$controlSnip->assign('controller_name', '');
		}
		
		return $controlSnip;
	}
	
	private function getFunctions()
	{
		if (!$this->admin) {
			return '';
		}
		
		$funSnip = $this->snippets->getSnippet('controller_functions');
		$funSnip->assign('component', $this->component);
		
		if ($this->component == $this->table) {
			$table = $this->table;
		} else {
			$table = $this->component . '_' . $this->table;
		}
		
		$funSnip->assign('table', $table);
		$funSnip->assign('tablename', ucfirst(str_replace('_', ' ', $this->table)));
		$funSnip->assign('controller_name', $this->defaultView);
		
		$addView = preg_replace('/_?list/i', '', $this->defaultView);
		$funSnip->assign('add_view', $addView);
		
		return $funSnip;
	}
}