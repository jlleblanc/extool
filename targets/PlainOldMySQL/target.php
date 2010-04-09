<?php
namespace Extool\Target;

class PlainOldMySQL implements TargetInterface
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
		$files = new \Extool\Helpers\FilePackage();

		$install_file = new \Extool\Helpers\File();
		$uninstall_file = new \Extool\Helpers\File();

		foreach ($this->rep->tables as $table) {
			$table->createDefaultKey();

			$mysql = new \Extool\Helpers\MySQL($table);
			$install_file->appendContents($mysql->generateCreate() . "\n\n");
			$uninstall_file->appendContents($mysql->generateDrop() . "\n");
		}

		$files->addFile('admin/install.mysql.sql', $install_file);
		$files->addFile('admin/uninstall.mysql.sql', $uninstall_file);

		return $files;
	}
}
