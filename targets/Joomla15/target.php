<?php
namespace Extool\Target;

class Joomla15 implements TargetInterface
{
	private $rep;
	private $files;
	private $snippets;
	private $config;

	function getConfiguration()
	{
		if (!isset($this->config)) {
			$fieldset = array(
				'component' => 'text',
				'project' => 'text',
				'name' => 'text',
				'license' => 'text',
				'version' => 'text',
				'description' => 'text',
				'date' => 'date',
				'author' => 'text',
				'email' => 'email',
				'copyright' => 'text'
			);

			$fields = new \Extool\Representation\Fields($fieldset);

			$this->config = new Configuration($fields);
		}

		return $this->config;
	}

	public function setConfiguration(Configuration $configuration)
	{
		if (!$this->config) {
			$this->getConfiguration();
		}

		if ($configuration->fields != $this->config->fields) {
			throw new \Exception("Configuration fields do not match the given ones");
		}

		$this->config = $configuration;
	}

	public function setRepresentation(\Extool\Representation\Representation $representation)
	{
		$this->rep = $representation;
	}

	public function generate()
	{
		$this->snippets = new \Extool\Helpers\SnippetFactory();
		$this->snippets->setBasePath('targets/Joomla15/snip');
		$this->files = new \Extool\Helpers\FilePackage();

		// TODO: There's probably a better way than this. I'm sure there's 
		// some ideal design pattern floating around out there that my
		// architecturally impoverished mind hasn't been exposed to.
		$this->generateMySQL();
		$this->makeTableClasses();
		$this->makeViews();
		$this->makeFrontController();
		$this->makeAdminControllers();
		$this->makeMainFiles();
		$this->makeModels();
		$this->makeManifest();

		return $this->files;
	}

	private function generateMySQL()
	{
		$component = $this->rep->name;
		$install_file = new \Extool\Helpers\File();
		$uninstall_file = new \Extool\Helpers\File();

		foreach ($this->rep->tables as $table) {
			$table->createDefaultKey();

			$mysql = new \Extool\Helpers\MySQL($table);

			if ($component == $table->system_name) {
				$mysql->setName('#__' . $table->system_name);
			} else {
				$mysql->setName('#__' . $component . '_' . $table->system_name);
			}

			$install_file->appendContents($mysql->generateCreate() . "\n\n");
			$uninstall_file->appendContents($mysql->generateDrop() . "\n");
		}

		$this->files->addFile('admin/install.mysql.sql', $install_file);
		$this->files->addFile('admin/uninstall.mysql.sql', $uninstall_file);
	}

	private function makeTableClasses()
	{
		foreach ($this->rep->tables as $table) {
			$table_name = strtolower(str_replace(' ', '_', $table->name));

			if ($table_name != $this->rep->name) {
				$table_name = $this->rep->name . '_' . $table_name;
			}

			$tableSnip = $this->snippets->getSnippet('table');
			$tableSnip->assign('table_name', $table_name);
			$tableSnip->assign('table_uc_name', ucfirst($table_name));
			$tableSnip->assign('table_key', $table->key);

			foreach ($table->fields as $field) {
				$field_snip = $this->snippets->getSnippet('table_field');
				$field_snip->assign('field', strtolower($field['name']));
				$tableSnip->add('variables', $field_snip);
			}

			$fileSnip = $this->snippets->getSnippet('code');
			$fileSnip->assign('code', $tableSnip);

			$tableFile = new \Extool\Helpers\File();
			$tableFile->setContents($fileSnip);
			$this->files->addFile("admin/tables/{$table_name}.php", $tableFile);
		}	
	}

	private function makeViews()
	{
		include_once 'helpers/view.inc';

		foreach ($this->rep->public_views as $view) {
			$codeView = new Joomla15View($view, $this->rep);
			$clean_view_name = str_replace(array(' ', '_'), '', $view->system_name);

			$path = "site/views/" . $clean_view_name . '/view.html.php';
			$this->files->addFile($path, $codeView->makeViewClass());

			$path = "site/views/" . $clean_view_name . '/tmpl/default.php';
			$this->files->addFile($path, $codeView->makeViewTmpl());
		}

		foreach ($this->rep->admin_views as $view) {
			$codeView = new Joomla15View($view, $this->rep, true);
			$clean_view_name = str_replace(array(' ', '_'), '', $view->system_name);

			$path = "admin/views/" . $clean_view_name . '/view.html.php';
			$this->files->addFile($path, $codeView->makeViewClass());

			$path = "admin/views/" . $clean_view_name . '/tmpl/default.php';
			$this->files->addFile($path, $codeView->makeViewTmpl());
		}
	}

	private function makeFrontController()
	{
		include_once 'helpers/controller.inc';

		$default_view = null;
		foreach ($this->rep->public_views as $view) {
			if ($view->type == 'list' && $default_view == null) {
				$default_view = $view;
			}
		}

		$controller = new Joomla15Controller($this->rep, $default_view);

		$fileSnip = $this->snippets->getSnippet('code');
		$fileSnip->assign('code', $controller->makeControllerCode());

		$this->files->addFile("site/controller.php", $fileSnip);
	}

	private function makeAdminControllers()
	{
		include_once 'helpers/controller.inc';

		foreach ($this->rep->admin_views as $viewkey => $view) {
			// This code is an ugly kluge to get the name of the first table
			// in the model with the same name as the view
			$model = $this->rep->admin_models[$viewkey];
			$table_record_names = array_keys($model->tables);

			if ($view->type == 'list') {
				$controller = new Joomla15Controller($this->rep, $view, true, $table_record_names[0]);

				$fileSnip = $this->snippets->getSnippet('code');
				$fileSnip->assign('code', $controller->makeControllerCode());

				$view_name = str_replace(array('_', ' '), '', $view->system_name);
				$filename = 'admin/controllers/' . $view_name . '.php';
				$this->files->addFile($filename, $fileSnip);
			}
		}
	}

	private function makeModels()
	{
		foreach ($this->rep->public_models as $model) {
			$this->makeModelFiles($model);
		}
		
		foreach ($this->rep->admin_models as $model) {
			$this->makeModelFiles($model, true);
		}
	}

	private function makeModelFiles($model, $admin = false)
	{
		$component = $this->rep->name;
		$modelName = str_replace(array(' ', '_'), '', strtolower($model->name));

		$modelSnip = $this->snippets->getSnippet('model');
		$modelSnip->assign('component', ucfirst($component));
		$modelSnip->assign('model', ucfirst($modelName));

		foreach ($model->tables as $table) {
			if ($table->system_name == $component) {
				$tableSQLName = $component;
			} else {
				$tableSQLName = $component . '_' . $table->system_name;
			}

			$modelSnip->add('dataVariables', "private $" . $table->system_name . ';');

			$model_function = $this->snippets->getSnippet('model_function');
			$model_function->assign('tableName', $table->system_name);
			$model_function->assign('tableCapsName', ucfirst($table->system_name));
			$query = 'SELECT ' . implode(', ', $table->fields->names) . ' FROM #__' . $tableSQLName;
			$model_function->assign('query', $query);

			$modelSnip->add('dataFunctions', $model_function);

			if ($admin) {
				$modelSaveFunc = $this->snippets->getSnippet('model_save_function');
				$modelSaveFunc->assign('table', $tableSQLName);

				$modelSnip->add('dataFunctions', $modelSaveFunc);
			}
		}

		$fileSnip = $this->snippets->getSnippet('code');
		$fileSnip->assign('code', $modelSnip);

		if ($admin) {
			$folder = 'admin';
		} else {
			$folder = 'site';
		}

		$this->files->addFile($folder . "/models/{$modelName}.php", $fileSnip);
	}


	private function makeMainFiles()
	{
		// Frontend main file
		$mainSnip = $this->snippets->getSnippet('main');
		$mainSnip->assign('component', ucfirst($this->rep->name));

		$fileSnip = $this->snippets->getSnippet('code');
		$fileSnip->assign('code', $mainSnip);

		$this->files->addFile("site/{$this->rep->name}.php", $fileSnip);

		// Backend main file
		$mainSnip = $this->snippets->getSnippet('main_admin');
		$mainSnip->assign('component', ucfirst($this->rep->name));

		$first = true;
		foreach ($this->rep->admin_views as $view) {
			if ($view->type == 'list') {
				$view_name = str_replace(array('_', ''), '', $view->system_name);
				$caseSnip = $this->snippets->getSnippet('main_admin_case');
				$caseSnip->assign('controller', $view_name);
				$mainSnip->add('cases', $caseSnip);

				if ($first) {
					$defaultController = $view_name;
					$first = false;
				}
			}
		}

		$mainSnip->assign('defaultcontroller', $defaultController);

		$fileSnip = $this->snippets->getSnippet('code');
		$fileSnip->assign('code', $mainSnip);

		$this->files->addFile("admin/{$this->rep->name}.php", $fileSnip);
	}

	public function makeManifest()
	{
		$config = $this->getConfiguration();

		$xmlSnip = $this->snippets->getSnippet('xml');

		$xmlSnip->assign('component', $config->project);
		$xmlSnip->assign('name', $config->name);
		$xmlSnip->assign('license', $config->license);
		$xmlSnip->assign('version', $config->version);
		$xmlSnip->assign('description', $config->description);
		$xmlSnip->assign('date', date('m/d/y'));
		$xmlSnip->assign('author', $config->author);
		$xmlSnip->assign('email', $config->email);
		$xmlSnip->assign('copyright', '© ' . date('Y'));

		foreach ($this->rep->admin_views as $view) {
			if ($view->type == 'list') {
				$snip = $this->snippets->getSnippet('xml_submenu_items');
				$snip->assign('component', 'com_' . $config->project);
				$snip->assign('title', ucwords($view->name));
				$snip->assign('view', str_replace('_', '', $view->system_name));
				$xmlSnip->add('submenu_items', $snip);
			}
		}

		$this->files->addFile($config->project . '.xml', $xmlSnip);
	}
}
