<?php
namespace Extool\Helpers;

/**
 * A Factory for loading up snippet files, then constructing snippet objects;
 * this should help performance, as well as keep the file loading aspect away
 * from the core Snippet class
 *
 * @package default
 * @author Joseph LeBlanc
 */
class SnippetFactory
{
	/**
	 * This array caches snippets in memory to prevent repeated disk reads
	 * for the same snippet.
	 *
	 * @var array
	 */
	private $snippet_contents = array();

	/**
	 * The base folder from which to pull snippets
	 *
	 * @var string
	 */
	private $base_path = false;

	/**
	 * Given a filename, this function returns a corresponding snippet object.
	 * It first checks to see if the snippet text has been loaded from disk. If
	 * so, it assembles a Snippet object using the cached text. Otherwise, the
	 * text is loaded and cached first.
	 *
	 * @param string $filename 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function getSnippet($filename)
	{
		if (!isset($this->snippet_contents[$filename])) {

			if ($this->base_path) {
				$full_file_path = $this->base_path . '/' . $filename;
			} else {
				$full_file_path = $filename;
			}

			$this->snippet_contents[$filename] = file_get_contents($full_file_path);
		}

		return new Snippet($this->snippet_contents[$filename]);
	}

	/**
	 * Used to set the root directory to start looking for snippets. Do not add
	 * the trailing slash.
	 *
	 * @param string $path 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setBasePath($path)
	{
		if(is_dir($path)) {
			$this->base_path = $path;
		} else {
			throw new \Exception("Path {$path} does not exist");
		}
	}
}
