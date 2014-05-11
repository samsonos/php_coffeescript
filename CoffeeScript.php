<?php
namespace samsonos\coffeescript;

use samson\core\ExternalModule;
use samson\resourcerouter;

/** Load CoffeeScript parser manually */
require 'src/CoffeeScript/Init.php';

/**
 * Class for loading module into SamsonPHP
 *
 * @package samsonos/coffeescript
 * @author Vitaly Iegorov <vitalyiegorov@gmail.com>
 * @version 0.1
 */
class CoffeeScript extends ExternalModule
{
	/** Identifier */
	protected $id = 'coffescript';
	
	/**	@see ModuleConnector::init() */
	public function init(array $params = array())
	{	
		// Pointer to resourcerouter			
		$rr = m('resourcer');

		// If .coffee resource has been updated
        $file = & $rr->updated['coffee'];
		if (isset($file)) try {
            // Read coffee file
            $coffee = file_get_contents($file);

            // Initialize coffee compiler
            \CoffeeScript\Init::load();

            // Read updated .coffee resource file and compile it
            $js = \CoffeeScript\Compiler::compile($coffee, array('filename' => $file));

			// Read other collected javascript file and write compiled coffee code to the same place
			file_put_contents($rr->cached['js'], file_get_contents($rr->cached['js']).$js);
		}
		catch( Exception $e){ e('Ошибка обработки CoffeeScript: '.$e->getMessage()); }
		
		// Call parent method
		parent::init($params);
	}	
}