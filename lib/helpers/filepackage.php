<?php
namespace Extool\Helpers;

/**
 * A class for holding files before they are written to disk.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class FilePackage
{
	private $fileroot;
	private $files;

	/**
	 * Optionally accepts a path (without trailing slash) where files should be
	 * written.
	 *
	 * @param string $fileroot 
	 * @author Joseph LeBlanc
	 */
	function __construct($fileroot = '')
	{
		if ($fileroot) {
			$this->setRoot($fileroot);
		}
	}

	/**
	 * Sets the path where files should be written. Expects a clean filepath,
	 * without the trailing slash.
	 *
	 * @param string $root 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setRoot($fileroot)
	{
		if (file_exists($fileroot)) {
			$this->fileroot = $fileroot;
		} else {
			throw new \Exception("File root {$fileroot} does not exist");
		}
	}

	/**
	 * Given the file root, this function writes all of the files in memory to
	 * disk.
	 *
	 * TODO: this may be best implemented through a helper function/class.
	 * 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function writeAll()
	{
		if (!isset($this->fileroot)) {
			throw new \Exception("The file root has not been set");
		}

		if (!isset($this->files)) {
			throw new \Exception("No files to write");
		}

		$this->writeFilesRecursively($this->files);
	}

	/**
	 * Given an array of file contents keyed by filename, this function will
	 * recursively work its way through the array and write out the files. When
	 * the value of an element in the array is another array, the recursion
	 * begins. The path is built as the function recurses.
	 *
	 * @param array $files 
	 * @param array $path 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function writeFilesRecursively($files, $path = array())
	{
		foreach ($files as $name => $file) {
			if (is_array($file)) {
				$path[] = $name;
				$this->writeFilesRecursively($file, $path);
			} else {
				$directory = $this->fileroot . '/' . implode('/', $path);

				if (!is_dir($directory)) {
					mkdir($directory);
				}

				$fullpath = $directory . '/' . $name;

				file_put_contents($fullpath, $file);
			}
		}
	}

	/**
	 * Adds a file into memory, given a path and the contents. This is held in
	 * memory before being written to disk; that way, you can keep resetting the
	 * file as necessary.
	 *
	 * @param string $path 
	 * @param string $contents 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addFile($path, File $file)
	{
		$path_array = explode('/', $path);

		$this->addFileRecursively($path_array, $file, $this->files);
	}

	/**
	 * A recursive function that checks and builds the path to a file, then
	 * sets the file in place if it doesn't already exist.
	 *
	 * @param array $path 
	 * @param File $file 
	 * @return boolean
	 * @author Joseph LeBlanc
	 */
	private function addFileRecursively($path, File $file, &$file_node)
	{
		$next_segment = array_shift($path);

		if (count($path) == 0) {
			if (isset($file_node[$next_segment])) {
				throw new \Exception("File {$next_segment} already exists");
			}

			$file_node[$next_segment] = $file;
			return true;
		} else if (isset($file_node[$next_segment])) {
			if ($file_node[$next_segment] instanceof File) {
				throw new \Exception("Invalid path: file {$next_segment} already exists");
			}
		} else {
			$file_node[$next_segment] = array();
		}

		return $this->addFileRecursively($path, $file, $file_node[$next_segment]);
	}

	/**
	 * Returns a nested array of File objects
	 *
	 * @return array
	 * @author Joseph LeBlanc
	 */
	public function getAll()
	{
		return $this->files;
	}
}
