<?php
namespace Extool\Target;

class View
{
	private $component;
	private $view;
	private $viewClass;
	private $viewDir;
	private $tables;
	private $tableNames;
	private $admin;
	
	private $templateType;
	private $dataType;
	private $listViews;
	
	function __construct($view, $component, $admin = false)
	{
		$this->view = $view;
		$this->admin = $admin;
		$this->component = $component;
		
		$this->viewDir = strtolower(str_replace(' ', '', $view));
		$this->viewClass = ucfirst($this->viewDir);
		$this->tableNames = array_keys($this->tables);
		
		if ($admin) {
			$this->listViews = array_keys($plan->listViews['admin']);
		} else {
			$this->listViews = array_keys($plan->listViews['front']);
		}

		foreach ($this->listViews as &$view) {
			$view = str_replace('_', '', $view);
		}
	}
	
	public function makeViewClass()
	{
		if ($this->determineDataType() == 'multiple') {
			return $this->makeMultipleViewClass();
		}
		
		return $this->makeSingleViewClass();
	}
	
	public function makeViewTmpl()
	{
		switch ($this->determineTemplateType()) {
			case 'adminlist':
				return $this->makeAdminListTmpl();
				break;
			
			case 'adminedit':
				return $this->makeAdminEditTmpl();
				break;
			
			case 'frontlist':
				return $this->makeFrontListTmpl();
				break;
			
			case 'frontsingle':
				return $this->makeFrontSingleTmpl();
				break;
		}
		
		
		return false;
	}
	
	private function makeAdminListTmpl()
	{
		$headers = array();
		$rowCols = array();
		
		$i = 0;
		foreach ($this->getTableFields() as $fieldTitle => $field) { 
			$header = new Snippet('listadmin_tmpl_header');
			$rowCol = new Snippet('listadmin_tmpl_rowcol');
			if ($i == 0) {
				$header->assign('{{class}}', ' class="title"');
				$rowCol->assign('{{preWrap}}', '<a href="<?php echo $link; ?>">');
				$rowCol->assign('{{postWrap}}', '</a>');
			} else {
				$header->assign('{{class}}', '');
				$rowCol->assign('{{preWrap}}', '');
				$rowCol->assign('{{postWrap}}', '');
			}
			
			$header->assign('{{fieldTitle}}', ucfirst($fieldTitle));
			$rowCol->assign('{{field}}', $field);
			
			$headers[] = $header;
			$rowCols[] = $rowCol;
			
			$i++;
		}
		
		$tmplSnip = new Snippet('listadmin_tmpl');
		
		$tmplSnip->assign('{{friendlyViewName}}', $this->view);
		$tmplSnip->assign('{{tablename}}', $this->tableNames[0]);
		$tmplSnip->assign('{{controller}}', $this->getDefaultController());
		$tmplSnip->assign('{{headers}}', $headers, "\n");
		$tmplSnip->assign('{{editviewname}}', preg_replace('/list$/', '', $this->viewDir));
		$tmplSnip->assign('{{datarows}}', $rowCols, "\n");
		
		return $tmplSnip;
	}
	
	private function makeAdminEditTmpl()
	{		
		foreach ($this->getTableFields() as $fieldTitle => $field) {
			$colSnip = new Snippet('editadmin_tmpl_row');
			$colSnip->assign('{{fieldTitle}}', ucfirst($fieldTitle));
			$colSnip->assign('{{fieldName}}', $field);
			$fields[] = $colSnip;
		}
		
		$tmplSnip = new Snippet('editadmin_tmpl');
		
		$tmplSnip->assign('{{friendlyViewName}}', $this->view);
		$tmplSnip->assign('{{controller}}', $this->getDefaultController());
		$tmplSnip->assign('{{fields}}', $fields);
		
		return $tmplSnip;
	}
	
	private function makeFrontSingleTmpl()
	{
		$fields = array();
		
		foreach ($this->getTableFields() as $fieldTitle => $fieldValue) {
			$field = new Snippet('singlefront_tmpl_fields');
			$field->assign('{{fieldTitle}}', ucfirst($fieldTitle));
			$field->assign('{{fieldValue}}', $fieldValue);
			$fields[] = $field;
		}
		
		$tmplSnip = new Snippet('singlefront_tmpl');
		$tmplSnip->assign('{{viewname}}', str_replace(' ', '_', strtolower($this->view)));
		$tmplSnip->assign('{{fields}}', $fields, "\n");
		
		return $tmplSnip;
	}
	
	private function makeFrontListTmpl()
	{
		$headers = array();
		$fields = array();
		
		$i = 0;
		foreach ($this->getTableFields() as $fieldTitle => $fieldValue) {
			$header = new Snippet('listfront_tmpl_header');
			$header->assign('{{header}}', $fieldTitle);
			$headers[] = $header;
			
			$field = new Snippet('listfront_tmpl_field');
			
			if ($i == 0) {
				$field->assign('{{field}}', "<a href=\"<?php echo \$link; ?>\"><?php echo \$row->{$fieldValue} ?></a>");
			} else {
				$field->assign('{{field}}', "<?php echo \$row->{$fieldValue} ?>");
			}
			
			$fields[] = $field;
			
			$i++;
		}
		
		$tmplSnip = new Snippet('listfront_tmpl');
		$tmplSnip->assign('{{headers}}', $headers, "\n");
		$tmplSnip->assign('{{fields}}', $fields, "\n");
		$tmplSnip->assign('{{viewname}}', str_replace(' ', '_', strtolower($this->view)));
		$tmplSnip->assign('{{tablename}}', $this->tableNames[0]);
		$tmplSnip->assign('{{singleviewname}}', preg_replace('/list$/i', '', $this->viewDir));
		
		return $tmplSnip;
	}
	
	private function makeSingleViewClass()
	{
		$viewSnip = new Snippet('view');
		$viewSnip->assign('{{component}}', ucfirst($this->component));
		$viewSnip->assign('{{view}}', $this->viewClass);
		
		$table = $this->tableNames[0];
		
		$rowCode = array();
		
		if ($this->determineTemplateType() == 'adminedit') {
			$dataSnip = new Snippet('view_adminedit');
		} else {
			$dataSnip = new Snippet('view_frontsingle');
		}
		
		if ($this->component != $table) {
			$table = $this->component . '_' . $table;
		} 
		
		$dataSnip->assign('{{table}}', $table);
		
		$viewSnip->assign('{{modelData}}', $dataSnip);
		
		$fileSnip = new Snippet('code');
		$fileSnip->assign('{{code}}', $viewSnip);
		
		return $fileSnip;
	}
	
	private function makeMultipleViewClass()
	{
		$viewSnip = new Snippet('view');
		$viewSnip->assign('{{component}}', ucfirst($this->component));
		$viewSnip->assign('{{view}}', $this->viewClass);
		
		$modelData = array();
		
		foreach ($this->tableNames as $table) {
			$getModel = new Snippet('view_getmodel');
			$getModel->assign('{{table}}', $table);
			$modelData[] = $getModel;
		}
		
		$viewSnip->assign('{{modelData}}', $modelData, "\n\n");
		
		$fileSnip = new Snippet('code');
		$fileSnip->assign('{{code}}', $viewSnip);
		
		return $fileSnip;
	}
	
	private function determineTemplateType()
	{
		if (isset($this->templateType)) {
			return $this->templateType;
		}
		
		$list = false;
		
		if (preg_match('/list$/i', $this->viewDir)) {
			$list = true;
		}
		
		if ($this->admin) {
			if ($list) {
				$this->templateType = 'adminlist';
			} else {
				$this->templateType = 'adminedit';
			}
		} else {
			if ($list) {
				$this->templateType = 'frontlist';
			} else {
				$this->templateType = 'frontsingle';
			}
		}
		
		return $this->templateType;
	}
	
	private function determineDataType()
	{
		if (isset($this->dataType)) {
			return $this->dataType;
		}
		
		$this->dataType = 'multiple';
		
		if (!preg_match('/list$/i', $this->viewDir) && count($this->tables) == 1) {
			
			$first = current($this->tables);
			
			if ($first[0] == '*') {
				$this->dataType = 'single';
			}
		}
		
		return $this->dataType;
	}
	
	// This helps us get the correct array of fields whether * or specific
	// fields are specified in the view
	private function getTableFields()
	{		
		$tableNames = array_keys($this->tables);
		$tableName = $tableNames[0];
		
		if ($this->tables[$tableName][0] == '*') {
			$extool = Extool::getInstance();
			$allTables = $extool->plan->tables;
			
			return $allTables[$tableName]['fields'];
		} 
		
		$fields = array();
		
		foreach ($this->tables[$tableName] as $field) {
			$field = ucfirst($field);
			$fields[$field] = strtolower(str_replace(' ', '_', $field));
		}
		
		return $fields;
	}
	
	private function getDefaultController()
	{
		$controller = $this->viewDir;
		
		if (!preg_match('/list$/i', $controller)) {
			if (in_array($controller . 'list', $this->listViews)) {	
				$controller .= 'list';
			}
		}
		
		return $controller;
	}
}
