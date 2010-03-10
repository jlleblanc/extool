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
			$tableSnip->assign('variables', $this->makeTableFields($table->fields));

			$fileSnip = $this->snippets->getSnippet('code');
			$fileSnip->assign('code', $tableSnip);

			$tableFile = new \Extool\Helpers\File();
			$tableFile->setContents($fileSnip);
			$this->files->addFile("admin/tables/{$table_name}.php", $tableFile);
		}	
	}

	private function makeTableFields($fields)
	{
		$newFields = array();
		
		foreach ($fields as $field) {
			$field = strtolower($field['name']);
						
			$newFields[] = "var \${$field} = null;";
		}
		
		return implode("\n\t", $newFields);
	}
}
