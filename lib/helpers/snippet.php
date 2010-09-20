<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */
namespace Extool\Helpers;

/**
 * Snippets are designed to search and replace text on small pieces of code.
 * You can assign strings, functions, or other Snippets to snippet fields.
 * Snippet fields are delimited with double curly braces, like this:
 * 
 * {{field}}
 *
 * @package default
 * @author Joseph LeBlanc
 */
class Snippet
{
	protected $contents;
	protected $fields = array();

	/**
	 * Requires the text contents of the Snippet.
	 *
	 * @param string $contents 
	 * @author Joseph LeBlanc
	 */
	function __construct($contents)
	{
		if (is_string($contents)) {
			$this->contents = $contents;
		} else {
			throw new \Exception("The Snippet class constructor requires strings, " . gettype($contents) . " given");
		}

		$this->extractFields();
	}

	/**
	 * Assigns a Snippet or string to the specified field, given that the
	 * field exists. Calling this function will overwrite any existing snippets
	 * or strings currently assigned to the field. Use Snippet::add() to append
	 * strings or snippets.
	 *
	 * @param string $field 
	 * @param mixed $contents 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function assign($field, $contents)
	{
		if (isset($this->fields[$field])) {
			// reset the field before adding the contents
			$this->fields[$field] = array();
			$this->add($field, $contents);
		} else {
			throw new \Exception("Field {$field} not present in Snippet");
		}
	}

	/**
	 * Adds the Snippet or string in $contents to the specified field, if the
	 * field exists.
	 *
	 * @param string $field 
	 * @param mixed $contents 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function add($field, $contents)
	{
		if ($contents instanceof Snippet || is_string($contents)) {
			if (isset($this->fields[$field])) {
				$this->fields[$field][] = $contents;
			} else {
				throw new \Exception("Could not add snippet: field {$field} is invalid");
			}
		} else {
			throw new \Exception("Could not add snippet: must be of type Snippet or string, " . gettype($contents) . " given.");
		}
	}

	/**
	 * Scans the contents of the Snippet for fields, marked with double curly
	 * braces. An example would be {{field}}.
	 *
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function extractFields()
	{
		preg_match_all('/\{\{(.*)\}\}/Ums', $this->contents, $matches);

		foreach ($matches[1] as $field) {
			if (!isset($this->fields[$field])) {
				$this->fields[$field] = array();
			}
		}
	}

	/**
	 * Cycles through all Snippets and strings assigned to fields in the 
	 * snippet, then returns a searched and replaced version of the Snippet.
	 *
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function __tostring()
	{
		$string = $this->contents;

		foreach ($this->fields as $field => $snips) {
			$spacing = '';
			// disappointed that I can't seem to correctly match whitespace
			// at the beginning of a line
			$pattern = '/([\t ]*)\{\{' . $field . '\}\}/';
			if (preg_match($pattern, $this->contents, $matches)) {
				$spacing = $matches[1];
			}

			$replacement = '';

			// Also add tabbing here
			$replacement .= implode("\n{$spacing}", $this->snippetArrayToLines($snips));

			$string = str_replace('{{' . $field . '}}', $replacement, $string);
		}

		return $string;
	}

	/**
	 * Returns an array of strings given an array of Snippet objects
	 *
	 * @param array $snips 
	 * @return array
	 * @author Joseph LeBlanc
	 */
	private function snippetArrayToLines($snips)
	{
		$lines = array();

		foreach ($snips as $snip) {
			$snip = explode("\n", $snip);
			$lines = array_merge($lines, $snip);
		}

		return $lines;
	}

	/**
	 * Allows the retrieval of the protected member variables. If $fields is
	 * specified, only the field names are specified
	 *
	 * @param string $name 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function __get($name)
	{
		if ($name == 'fields') {
			return array_keys($this->fields);
		}

		return $this->$name;
	}
}
