<?php

include_once 'lib/target.inc';

class Joomla15Target extends Target
{
	function __construct(&$plan)
	{
		$component = $plan->project;
		
		$this->front = "site/";
		$frontFiles = array(
			"{$component}.php",
			"controller.php",
			);
		
		$this->admin = "admin/";
		$adminFiles = array(
			"{$component}.php",
			"install.mysql.sql",
			"uninstall.mysql.sql"
			);
		
		$this->addFileArray($frontFiles, $this->front);
		$this->addFileArray($adminFiles, $this->admin);
		$this->addFile("{$component}.xml");
		
		$this->prefix = '#__';
		
		parent::__construct();
	}
	
	public function generate()
	{
		$this->makeSQL();
		$this->makeTableClass();
		$this->makeViews();
		$this->makeModels();
		$this->makeFrontController();
		$this->makeAdminController();
		$this->makeManifest();
		$this->makeMainFiles();
		
		$this->buildTarget();
	}
	
	private function makeSQL()
	{		
		// Installation SQL
		$tables = $this->getTableSQL();
		
		$sql = implode("\n\n", $tables);
		
		$this->assignSnippet($sql, "install.mysql.sql", $this->admin);
		
		// Deinstallation SQL
		$drops = $this->getDropTableSQL();
		$this->assignSnippet($drops, "uninstall.mysql.sql", $this->admin);
	}
	
	private function makeTableClass()
	{
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		$component = $plan->project;
		$tables = $plan->tables;
		
		$snippets = array();
				
		foreach ($tables as $table => $info) {
			if ($table != $component) {
				$table = $component . '_' . $table;
			}
			
			$tableSnip = new Snippet('table');
			$tableSnip->assign('{{table_name}}', $table);
			$tableSnip->assign('{{table_uc_name}}', ucfirst($table));
			$tableSnip->assign('{{variables}}', $this->makeTableFields($info['fields']));
			
			$fileSnip = new Snippet('code');
			$fileSnip->assign('{{code}}', $tableSnip);
			$this->addFile("{$table}.php", $this->admin . 'tables/');
			$this->assignSnippet($fileSnip, "{$table}.php", $this->admin . 'tables/');
		}
		
	}
	
	/*
		TODO: might want to snippetize this (as well as similar functions) into class extensions
	*/
	private function makeTableFields(&$fields)
	{
		$newFields = array();
		
		foreach ($fields as $field) {
			$field = explode(' ', $field);
			$field = $field[0];
						
			$newFields[] = "var \${$field} = null;";
		}
		
		return implode("\n\t", $newFields);
	}
	
	private function makeFrontController()
	{
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		$listViews = array_keys($plan->listViews['front']);
		
		include_once 'helpers/controller.inc';

		$defaultView = '';
		if (isset($listViews[0])) {
			$defaultView = $listViews[0];
		}

		$controller = new Joomla15Controller($defaultView);
				
		$fileSnip = new Snippet('code');
		$fileSnip->assign('{{code}}', $controller->makeControllerCode());
		
		$this->assignSnippet($fileSnip, "controller.php", $this->front);
	}
	
	private function makeAdminController()
	{
		include_once 'helpers/controller.inc';
		
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		
		foreach ($plan->listViews['admin'] as $view => $tables) {
			$tables = array_keys($tables);
			$view = str_replace(array(' ', '_'), '', ucwords($view));
			$controller = new Joomla15Controller($view, true, $tables[0]);
			
			$fileSnip = new Snippet('code');
			$fileSnip->assign('{{code}}', $controller->makeControllerCode());
			
			$this->addFile(strtolower($view) . '.php', $this->admin . "controllers/");
			$this->assignSnippet($fileSnip, strtolower($view) . '.php', $this->admin . 'controllers/');
		}
	}
	
	private function makeViews()
	{
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		
		include_once 'helpers/view.inc';
		
		foreach ($plan->views as $view => $tables) {
			$codeView = new Joomla15View($view, $tables);
			$this->makeViewFiles($codeView);
		}
		
		foreach ($plan->adminViews as $view => $tables) {
			$codeView = new Joomla15View($view, $tables, true);
			$this->makeViewFiles($codeView, true);
		}		
	}
	
	private function makeViewFiles($codeView, $admin = false)
	{
		if ($admin) {
			$path = $this->admin;
		} else {
			$path = $this->front;
		}
		
		$this->addFile("view.html.php", $path . "views/{$codeView->viewDir}/");
		$this->addFile("default.php", $path . "views/{$codeView->viewDir}/tmpl/");
			
		$this->assignSnippet($codeView->makeViewClass(), "view.html.php", $path . "views/{$codeView->viewDir}/");
		$this->assignSnippet($codeView->makeViewTmpl(), "default.php", $path . "views/{$codeView->viewDir}/tmpl/");
	}
	
	private function makeModels()
	{
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		$component = ucfirst($plan->project);
		
		foreach ($this->getModels() as $modelName => $model) {
			$this->makeModelFiles($component, $modelName, $model);
		}
		
		foreach ($this->getModels('admin') as $modelName => $model) {
			$this->makeModelFiles($component, $modelName, $model, true);
		}
	}
	
	private function makeModelFiles($component, $modelName, $model, $admin = false)
	{
		$modelName = str_replace(' ', '', strtolower($modelName));
		
		if ($admin) {
			$path = $this->admin;
		} else {
			$path = $this->front;
		}
		
		$this->addFile("{$modelName}.php", $path . "models/");
		
		$modelSnip = new Snippet('model');
		$modelSnip->assign('{{component}}', $component);
		$modelSnip->assign('{{model}}', ucfirst($modelName));
		
		$tables = $model->getModelSQL();
		
		$modelVariables = array();
		$modelFucntions = array();
		
		foreach ($tables as $tableName => $query) {
			$modelVariables[] = "\tprivate $" . $tableName . ';';
			
			$model_function = new Snippet('model_function');
			$model_function->assign('{{tableName}}', $tableName);
			$model_function->assign('{{tableCapsName}}', ucfirst($tableName));
			$model_function->assign('{{query}}', $query);
			$modelFunctions[] = $model_function;
		}
		
		$modelSnip->assign('{{dataVariables}}', $modelVariables, "\n");
		$modelSnip->assign('{{dataFunctions}}', $modelFunctions, "\n");
		
		$fileSnip = new Snippet('code');
		$fileSnip->assign('{{code}}', $modelSnip);
		
		$this->assignSnippet($fileSnip, "{$modelName}.php", $path . "models/");
	}
	
	private function makeMainFiles()
	{
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		$component = ucfirst($plan->project);
		
		// Frontend main file
		$mainSnip = new Snippet('main');

		$fileSnip = new Snippet('code');
		$fileSnip->assign('{{code}}', $mainSnip);
		$fileSnip->assign('{{component}}', $component);
		
		$this->assignSnippet($fileSnip, "{$plan->project}.php", $this->front);
		
		// Backend main file
		$mainSnip = new Snippet('main_admin');
		$mainSnip->assign('{{component}}', $component);
		
		$cases = array();
		
		$i = 0;
		foreach (array_keys($plan->listViews['admin']) as $view) {
			$view = str_replace('_', '', $view);
			$caseSnip = new Snippet('main_admin_case');
			$caseSnip->assign('{{controller}}', $view);
			$cases[] = $caseSnip;
			
			if ($i == 0) {
				$defaultController = $view;
			}
			
			$i++;
		}
		
		$mainSnip->assign('{{cases}}', $cases, "\n");
		$mainSnip->assign('{{defaultcontroller}}', $defaultController);
		
		$fileSnip = new Snippet('code');
		$fileSnip->assign('{{code}}', $mainSnip);
		
		$this->assignSnippet($fileSnip, "{$plan->project}.php", $this->admin);
	}
	
	private function makeManifest()
	{
		$extool = Extool::getInstance();
		$plan = $extool->getPlan();
		
		$xmlSnip = new Snippet('xml');
		
		$xmlSnip->assign('{{component}}', $plan->project);
		$xmlSnip->assign('{{name}}', $plan->name);
		$xmlSnip->assign('{{license}}', $plan->license);
		$xmlSnip->assign('{{version}}', $plan->version);
		$xmlSnip->assign('{{description}}', $plan->description);
		$xmlSnip->assign('{{date}}', date('m/d/y'));
		$xmlSnip->assign('{{author}}', ExtoolConfig::$name);
		$xmlSnip->assign('{{email}}', ExtoolConfig::$email);
		$xmlSnip->assign('{{copyright}}', '© ' . date('Y'));
		
		$submenuItems = array();
		
		foreach ($plan->listViews['admin'] as $view => $value) {
			$snip = new Snippet('xml_submenu_items');
			$snip->assign('{{component}}', 'com_' . $plan->project);
			$snip->assign('{{title}}', ucwords(str_replace('_', ' ', $view)));
			$snip->assign('{{view}}', str_replace('_', '', $view));
			$submenuItems[] = $snip;
		}
		
		$xmlSnip->assign('{{submenu_items}}', $submenuItems, "\n");
		
		$this->assignSnippet($xmlSnip, $plan->project . '.xml', '/');
	}
}