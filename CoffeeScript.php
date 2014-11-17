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

        // If we have coffee resource in project
        if (isset($rr->cached['coffee'])) {

            // Change coffee file to js and store it as current js resource
            $newJS = str_replace('.coffee', '.js', str_replace(__SAMSON_PUBLIC_PATH, '', $rr->cached['coffee']));

            // If .coffee resource has been updated
            $file = & $rr->updated['coffee'];
            if (isset($file)) try {

                // Read coffee file
                $coffee = file_get_contents($file);

                // Initialize coffee compiler
                \CoffeeScript\Init::load();

                // Read updated .coffee resource file and compile it
                $js = \CoffeeScript\Compiler::compile($coffee, array('filename' => $file));

                // Compile coffee script to js and save to the same location
                file_put_contents($file, $js);
            }
            catch( Exception $e){ e('Ошибка обработки CoffeeScript: '.$e->getMessage()); }

            // If regular JS has been updated or coffee script has been updated
            if (isset($rr->updated['js']) || isset($rr->updated['coffee'])) {
                // Read gathered js
                $oldJS = file_get_contents(str_replace(__SAMSON_PUBLIC_PATH, '', $rr->cached['js']));

                // Read gathered coffee
                $coffee = file_get_contents(str_replace(__SAMSON_PUBLIC_PATH, '', $rr->cached['coffee']));

                // Concatenate regular js and compiled coffee script js to a new javascript file
                file_put_contents($newJS, $oldJS.$coffee);
            }

            // Change old js resource to new one
            //$rr->cached['js'] = $newJS;
        }
		
		// Call parent method
		parent::init($params);
	}	
}