<?php
/**
 * API
 *
 * Shopp's Application Programming Interface library manager
 *
 * @author Jonathan Davis
 * @version 1.0
 * @copyright Ingenesis Limited, May 12, 2011
 * @license GNU GPL version 3 (or later) {@see license.txt}
 * @package shopp
 * @since 1.0
 * @subpackage shopp
 **/

interface ShoppAPI {
	function _apicontext(); // returns the correct contextual object, if possible
}

/**
 * ShoppAPILoader
 *
 * @author Jonathan Davis
 * @since 1.2
 * @package shopp
 **/
class ShoppAPIModules extends ModuleLoader {

	protected $loader = 'ShoppAPIFile';

	/**
	 * API constructor
	 *
	 * @author Jonathan Davis
	 *
	 * @return void
	 **/
	function __construct () {
		$this->path = SHOPP_THEME_APIS;

		$this->installed(); // Find modules
		$this->load(true); 	// Load all

	}

} // END class ShoppAPILoader

class ShoppAPIFile extends ModuleFile {

	function load () {
		include_once($this->file);
		$this->register();
	}

	function register () {
		// Hook _context
		$api = $this->subpackage;
		$API = new $api;
		$apicontext = $API->_apicontext();

		$setobject_call = method_exists($api,'_setobject')?array($api, '_setobject'):array($this,'setobject');
		add_filter('shopp_themeapi_object', $setobject_call, 10, 2);

		// Define a static $map property as an associative array or tag => member function names.
		// Without the tag key, it will be registered as a general purpose filter for all tags in this context
		if (!empty($API->$register)) {
			foreach ( $API->$register as $tag => $method ) {
				if ( is_callable(array($api, $method)) ) {
					if ( is_numeric($tag) ) add_filter( 'shopp_themeapi_'.strtolower($apicontext), array($api, $method), 10, 4 ); // general filter
					else add_filter( 'shopp_themeapi_'.strtolower($apicontext.'_'.$tag), array($api, $method), 10, 3 );
				}
			}
			return;
		}

		// Otherwise, the register function will assume that all method names (excluding _ prefixed methods) correspond to tag you want.
		// _ prefix members can be used as helper functions
		$methods = array_filter( get_class_methods ($api), create_function( '$m','return ( "_" != $m{0} );' ) );
		foreach ( $methods as $tag )
			add_filter( 'shopp_themeapi_'.strtolower($apicontext.'_'.$tag), array($api, $tag), 10, 3 );

	}

	function setobject ($Object,$context) {
		$api = new $this->subpackage;
		$apicontext = $api->_apicontext();

		if (strtolower($context) != strtolower($apicontext)) return $Object; // do nothing
		if (is_object($Object) && $apicontext == get_class($Object)) return $Object;  // still do nothing

		global $Shopp;
		$property = ucfirst($apicontext);
		if (property_exists($Shopp,$property))
			return $Shopp->{$property};

		return false;
	}

}

?>