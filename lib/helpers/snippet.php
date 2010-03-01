<?php
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

	function __construct($contents)
	{
		if (is_string($contents)) {
			$this->contents = $contents;
		} else {
			throw new \Exception("The Snippet class constructor requires strings, " . gettype($contents) . " given");
		}

		$this->extractFields();
	}

	public function assign($field, $contents)
	{
		if (isset($this->fields[$field])) {
			// reset the field before adding the contents
			$this->fields[$field] = array();
			$this->add($field, $contents);
		}
	}

	public function add($field, $contents)
	{
		if ($contents instanceof Snippet || is_string($contents)) {
			if (isset($this->fields[$field])) {
				$this->fields[$field][] = $contents;
			} else {
				throw new Exception("Could not add snippet: field {$field} is invalid");
			}
		} else {
			throw new Exception("Could not add snippet: must be of type Snippet or string, " . gettype($contents) . "given.");
		}
	}

	public function extractFields()
	{
		preg_match_all('/\{\{(.*)\}\}/Ums', $this->contents, $matches);

		foreach ($matches[1] as $field) {
			if (!isset($this->fields[$field])) {
				$this->fields[$field] = array();
			}
		}
	}

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
			$replacement .= implode("\n{$spacing}", $snips);

			$string = str_replace('{{' . $field . '}}', $replacement, $string);
		}

		return $string;
	}

	public function __get($name)
	{
		if ($name == 'fields') {
			return array_keys($this->fields);
		}

		return $this->$name;
	}
}
