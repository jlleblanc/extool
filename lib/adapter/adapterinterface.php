<?php
namespace Extool\Adapter;

/**
 * Adapters implement this interface so that resource names can be set and 
 * parsed, and so that representations can be prepared,
 *
 * @package default
 * @author Joseph LeBlanc
 */
interface AdapterInterface {

	/**
	 * Sends an representation object to the Adapter to be set with public 
	 * views, public models, admin views, admin models, tables, and data.
	 * The public items and tables are required.
	 *
	 * @param Representation $representation 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function decorateRepresentation(\Extool\Representation\Representation &$representation);

	/**
	 * Allows Extool to set the resource (such as filename) for the adapter 
	 * to use.
	 *
	 * @param mixed $resource 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setResource($resource);

	/**
	 * Tells the Adapter to parse the resource
	 *
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function parse();
}