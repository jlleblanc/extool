<?php
namespace Extool\Helpers;

/**
 * A class for generating MySQL queries, based on a Table class
 *
 * @package default
 * @author Joseph LeBlanc
 */
class MySQL
{
	protected $table;

	protected $name;

	protected $data;

	protected $snippets;

	private $lowercase = true;

	/**
	 * Requires a Table object for generating SQL
	 *
	 * @param Table $table 
	 * @author Joseph LeBlanc
	 */
	function __construct(\Extool\Representation\Table $table)
	{
		$this->table = $table;
		$this->setName($table->name);
		$this->snippets = new SnippetFactory();
		$this->snippets->setBasePath('lib/snippets/mysql');
	}

	/**
	 * Sets an SQL-friendly name for the table.
	 *
	 * @param string $name 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setName($name)
	{
		$name = str_replace(' ', '_', $name);
		$this->name = preg_replace('/[^A-Za-z_0-9#]/', '', $name);
		$this->name = strtolower($this->name);
	}

	/**
	 * Given a Data object, this function will add the data to the MySQL
	 * object. If the MySQL object already contains a Data object, the two
	 * objects are merged together.
	 *
	 * @param Data $data 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function addData(\Extool\Representation\Data $data)
	{
		if ($data->fields != $this->table->fields) {
			throw new \Exception("Data fields do not match Table fields");
		}

		if (isset($this->data)) {
			$this->data->mergeIn($data);
		} else {
			$this->data = $data;
		}
	}

	/**
	 * Given an Extool type, this function returns a corresponding MySQL column
	 * type.
	 *
	 * @param string $type 
	 * @return string
	 * @author Joseph LeBlanc
	 */
	public function getSQLType($type)
	{
		static $type_map = array(
			'text' => 'VARCHAR(255)',
			'long text' => 'LONGTEXT',
			'html' => 'LONGTEXT',
			'url'  => 'VARCHAR(255)', // might want to make this larger
			'email'  => 'VARCHAR(255)',
			'integer' => 'INT(11)',
			'decimal' => 'FLOAT',
			'money' => 'FLOAT(9,2)',
			'date' => 'DATETIME',
			'color_hex' => 'VARCHAR(7)',
			);

		return $type_map[$type];
	}

	/**
	 * Generates MySQL CREATE TABLE statements
	 *
	 * @return Snippet
	 * @author Joseph LeBlanc
	 */
	public function generateCreate()
	{
		$tableSnip = $this->snippets->getSnippet('table');
		$tableSnip->assign('table', $this->name);

		// Add the key first, because people expect it there.
		if ($this->table->key) {
			if ($this->lowercase) {
				$key = strtolower($this->table->key);
			} else {
				$key = $this->table->key;
			}

			$field = $this->snippets->getSnippet('field');
			$field->assign('field', $key);
			$field->assign('definition', 'SERIAL');
			$tableSnip->add('fields', $field);

			$field = $this->snippets->getSnippet('key');
			$field->assign('key_name', $key);
			$tableSnip->add('key', $field);
		}

		foreach ($this->table->fields as $field_info) {
			if ($field_info['name'] != $this->table->key) {
				$field = $this->snippets->getSnippet('field');

				if ($this->lowercase) {
					$field_name = strtolower($field_info['name']);
				} else {
					$field_name = $field_info['name'];
				}

				$field->assign('field', $field_name);
				$field->assign('definition', $this->getSQLType($field_info['type']));
				$tableSnip->add('fields', $field);
			}
		}

		return $tableSnip;
	}

	/**
	 * Generates MySQL DROP TABLE statements
	 *
	 * @return Snippet
	 * @author Joseph LeBlanc
	 */
	public function generateDrop()
	{
		$field = $this->snippets->getSnippet('drop');
		$field->assign('table', $this->name);

		return $field;
	}

	/**
	 * Generates MySQL INSERT statements, if data for the MySQL object is set.
	 *
	 * @return Snippet
	 * @author Joseph LeBlanc
	 */
	public function generateInsert()
	{
		if (!isset($this->data)) {
			throw new \Exception("No Data to be used for generating inserts");
		}

		$insertSnip = $this->snippets->getSnippet('insert');
		$insertSnip->assign('table_name', $this->name);

		$used = $this->getUsedDataFields();

		foreach ($used as &$field) {
			$field = '`' . $field . '`';
		}

		$field_names = implode(',', $used);

		$insertSnip->assign('field_names', $field_names);

		$sql = array();

		foreach ($this->data as $row) {
			foreach ($row as &$value) {
				$value = "'" . mysql_escape_string($value) . "'";
			}

			$sql[] = '(' . implode(',', $row) . ')';
		}

		$insertSnip->assign('values', implode(',', $sql) . ';');

		return $insertSnip;
	}

	/**
	 * Sets the $lowercase property. If true, all column names will be
	 * generated in lowercase
	 *
	 * @param boolean $lowercase 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setLowercase($lowercase = true)
	{
		if ($lowercase) {
			$this->lowercase = true;
		}

		$this->lowercase = false;
	}

	/**
	 * MySQL doesn't let us just add fields in at random, everything has to
	 * line up. So this function returns an ordered lookup array to guide
	 * the generation of INSERTs.
	 *
	 * @return array
	 * @author Joseph LeBlanc
	 */
	private function getUsedDataFields()
	{
		$used = array();

		foreach ($this->data as $row) {
			$first_row_fields = array_keys($row);
			break;
		}

		foreach ($first_row_fields as $friendly) {
			$used[$friendly] = $this->data->fields->names[$friendly];
		}

		return $used;
	}

	/**
	 * Allows access to all properties without allowing write access.
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
