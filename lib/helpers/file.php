<?php
namespace Extool\Helpers;

/**
 * This class represents a file. You can set the contents to either a string
 * or a snippet. When using strings, you can keep appending the file contents
 * as you build the file.
 *
 * @package default
 * @author Joseph LeBlanc
 */
class File
{
	/**
	 * Either a string or a Snippet with the intended contents of the file.
	 *
	 * @var mixed
	 */
	protected $contents;

	/**
	 * Optionally accepts the contents of the file as an argument
	 *
	 * @param mixed $contents 
	 * @author Joseph LeBlanc
	 */
	function __construct($contents = '')
	{
		if ($contents) {
			$this->setContents($contents);
		}
	}

	/**
	 * Accepts either a string or a Snippet to set as the contents of the File.
	 *
	 * @param mixed $contents 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setContents($contents)
	{
		if (is_string($contents) || ($contents instanceof Snippet)) {
			$this->contents = $contents;
		} else {
			throw new Exception("File contents must be a string or Snippet");
		}
	}

	/**
	 * If the File contents are of type string, $contents will be appended to
	 * the end of the current contents. If the contents of the File is not a
	 * string, an exception will be thrown.
	 *
	 * @param string $contents 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function appendContents(string $contents)
	{
		if (isset($this->contents)) {
			if ($this->contents instanceof Snippet) {
				throw new Exception("The file contains a Snippet, you may only append to strings");
			} else {
				$this->contents .= $contents;
			}
			
		} else {
			$this->setContents($contents);
		}
	}

	/**
	 * The file should act as a string, returning the contents. If contents is
	 * set as a Snippet, the __tostring() function of Snippet will also get
	 * called.
	 *
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function __tostring()
	{
		return $this->contents;
	}

	/**
	 * Allows access to contents without being able to set it directly.
	 *
	 * @param string $name 
	 * @return mixed
	 * @author Joseph LeBlanc
	 */
	public function __get($name)
	{
		return $this->$name;
	}
}
