<?php
namespace Extool\Target

/**
 * An interface for getting and setting the target's configration, setting
 * a representation, and generating the target code
 *
 * @package default
 * @author Joseph LeBlanc
 */
interface TargetInteface
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
	 * undocumented function
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
	public function setRepresentation(Representation $representation);

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
