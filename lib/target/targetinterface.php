<?php
/**
 * Extool Code Generator
 * 
 * Copyright 2010 Joseph LeBlanc
 * See LICENSE file for licensing details.
 * 
 */
namespace Extool\Target;

/**
 * An interface for getting and setting the target's configration, setting
 * a representation, and generating the target code
 *
 * @package default
 * @author Joseph LeBlanc
 */
interface TargetInterface 
{
	/**
	 * Returns a Configuration object outlining any data required by the 
	 * target.
	 *
	 * @return Configuration
	 * @author Joseph LeBlanc
	 */
	public function getConfiguration();

	/**
	 * Sets the configuration object after it has been decorated by the
	 * application.
	 *
	 * @param Configuration $configuration 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setConfiguration(Configuration $configuration);

	/**
	 * Receives a Representation object so it can be set in the target for 
	 * further use.
	 *
	 * @param Representation $representation 
	 * @return void
	 * @author Joseph LeBlanc
	 */
	public function setRepresentation(\Extool\Representation\Representation $representation);

	/**
	 * If the Representation is properly set, this function generates the code
	 * It returns a FilePackage object; this way, the target is not responsible
	 * for the filesystem.
	 *
	 * @return FilePackage
	 * @author Joseph LeBlanc
	 */
	public function generate();
}
