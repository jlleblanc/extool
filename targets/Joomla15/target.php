<?php
namespace Extool\Target;

class Joomla15 implements \Extool\Target\TargetInterface
{
	private $rep;
	private $files;
	private $snippets;

	public function setRepresentation(\Extool\Representation\Representation $representation)
	{
		$this->rep = $representation;
	}

	public function generate()
	{
		$this->snippets = new \Extool\Helpers\SnippetFactory();
		$this->snippets->setBasePath('targets/Joomla15/snip');
		$this->files = new \Extool\Helpers\FilePackage();

		// TODO: There's probably a better way than this
		$this->generateMySQL();
		$this->makeTableClasses();
		$this->makeViews();
		$this->makeFrontController();
		$this->makeAdminControllers();
		$this->makeMainFiles();
		$this->makeModels();

		return $this->files;
	}

	private function generateMySQL()
	{
		$install_file = new \Extool\Helpers\File();
		$uninstall_file = new \Extool\Helpers\File();

		foreach ($this->rep->tables as $table) {
			$table->createDefaultKey();

			$mysql = new \Extool\Helpers\MySQL($table);
			$mysql->setName('#__' . $table->name);
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
			$clean_view_name = strtolower(str_replace(' ', '_', $view->name));

			$path = "site/views/" . $clean_view_name . '/view.html.php';
			$this->files->addFile($path, $codeView->makeViewClass());

			$path = "site/views/" . $clean_view_name . '/tmpl/default.php';
			$this->files->addFile($path, $codeView->makeViewTmpl());
		}

		foreach ($this->rep->admin_views as $view) {
			$codeView = new Joomla15View($view, $this->rep, true);
			$clean_view_name = strtolower(str_replace(' ', '_', $view->name));

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
		
		foreach ($this->rep->admin_views as $view) {
			if ($view->type == 'list') {
				$view_name = str_replace(array(' ', '_'), '', ucwords($view->name));
				$controller = new Joomla15Controller($this->rep, $view, true);

				$fileSnip = $this->snippets->getSnippet('code');
				$fileSnip->assign('code', $controller->makeControllerCode());

				$filename = 'admin/controllers/' . strtolower($view_name) . '.php';
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
		$modelName = str_replace(' ', '_', strtolower($model->name));

		$modelSnip = $this->snippets->getSnippet('model');
		$modelSnip->assign('component', ucfirst($component));
		$modelSnip->assign('model', ucfirst($modelName));

		foreach ($model->tables as $table) {
			$modelSnip->add('dataVariables', "private $" . $table->name . ';');

			$model_function = $this->snippets->getSnippet('model_function');
			$model_function->assign('tableName', $table->name);
			$model_function->assign('tableCapsName', ucfirst($table->name));
			$model_function->assign('query', ' ');

			$modelSnip->add('dataFunctions', $model_function);
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
			$view_name = str_replace('_', '', $view->name);
			$caseSnip = $this->snippets->getSnippet('main_admin_case');
			$caseSnip->assign('controller', $view_name);
			$mainSnip->add('cases', $caseSnip);

			if ($first) {
				$defaultController = $view_name;
				$first = false;
			}
		}

		$mainSnip->assign('defaultcontroller', $defaultController);

		$fileSnip = $this->snippets->getSnippet('code');
		$fileSnip->assign('code', $mainSnip);

		$this->files->addFile("admin/{$this->rep->name}.php", $fileSnip);
	}
}
