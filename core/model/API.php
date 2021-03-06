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

defined( 'WPINC' ) || header( 'HTTP/1.1 403' ) & exit; // Prevent direct access

interface ShoppAPI {
	public static function _apicontext(); // returns the correct contextual object, if possible
}

final class ShoppDeveloperAPI {

	static $core = array(
		'core', 'theme', 'remote', 'script',
		'asset', 'cart', 'collection', 'customer',
		'meta', 'order', 'product', 'settings'
	);

	// Load public development API
	public static function load ( $basepath, $load = array() ) {
		$path = realpath("$basepath/api");

		$custom = apply_filters('shopp_developerapi_files',array());

		// Add custom Developer API files to core
		$files = array_merge(self::$core,$custom);

		// Make sure requested APIs exist
		$apis = array_intersect($files,$load);

		// If requested APIs are empty, use defaults instead
		if ( empty($apis) ) $apis = $files;

		foreach ( $apis as $api ) {
			if ( false === strpos($api,'.php') )
				require "$path/$api.php";
			else include $api;
		}
	}

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
	public function __construct () {
		$this->path = SHOPP_THEME_APIS;

		$this->installed(); // Find modules
		$this->load(true);  // Load all

		add_action('shopp_init', __CLASS__ . '::functions');
	}

	/**
	 * Loads the theme templates `shopp/functions.php` if present
	 *
	 * If theme content templates are enabled, checks for and includes a functions.php file (if present).
	 * This allows developers to add Shopp-specific presentation logic with the added convenience of knowing
	 * that shopp_init has run.
	 *
	 * @author Barry Hughes
	 * @since 1.3
	 *
	 * @return void
	 **/
	public static function functions () {
		if ( ! shopp_setting_enabled('theme_templates') ) return;
		locate_shopp_template( array('functions.php'), true );
	}

} // END class ShoppAPILoader

class ShoppAPIFile extends ModuleFile {

	public function load () {
		require $this->file;
		add_action( 'shopp_init', array($this, 'register') );
	}

	public function register () {
		// Hook _context
		$api = $this->subpackage;
		$apicontext = call_user_func(array($api,'_apicontext'));

		$setobject_call = method_exists($api,'_setobject')?array($api, '_setobject'):array($this,'setobject');
		add_filter('shopp_themeapi_object', $setobject_call, 10, 2);

		// Define a static $map property as an associative array or tag => member function names.
		// Without the tag key, it will be registered as a general purpose filter for all tags in this context
		$register = get_class_property($api,'register');
		if (!empty($register)) {
			foreach ( $register as $tag => $method ) {
				if ( strpos($method, '::') !== false ) list($apiclass, $method) = explode('::', $method);
				else $apiclass = $api;

 				if ( is_callable( array($apiclass, $method) ) ) {
					if ( is_numeric($tag) ) add_filter( 'shopp_themeapi_'.strtolower($apicontext), array($apiclass, $method), 9, 4 ); // general filter
					else add_filter( 'shopp_themeapi_'.strtolower($apicontext.'_'.$tag), array($apiclass, $method), 9, 3 );
				}
			}
			return;
		}

		// Otherwise, the register function will assume that all method names (excluding _ prefixed methods) correspond to tag you want.
		// _ prefix members can be used as helper functions
		$methods = array_filter( get_class_methods ($api), create_function( '$m','return ( "_" != $m{0} );' ) );
		foreach ( $methods as $tag )
			add_filter( 'shopp_themeapi_'.strtolower($apicontext.'_'.$tag), array($api, $tag), 9, 3 );

	}

	public function setobject ($Object,$context) {
		if ( is_object($Object) ) return $Object;  // always use if first argument is an object

		$api = $this->subpackage;
		$apicontext = call_user_func(array($api,'_apicontext'));

		if (strtolower($context) != strtolower($apicontext)) return $Object; // do nothing

		global $Shopp;
		$property = ucfirst($apicontext);
		if (property_exists($Shopp,$property))
			return $Shopp->{$property};

		return false;
	}

}


/**
 * ShoppRemoteAPIModules
 *
 * Loader for builtin RemoteAPI handlers
 *
 * @author Jonathan Davis
 * @since 1.3
 * @package shopp
 **/
class ShoppRemoteAPIModules extends ModuleLoader {

	protected $loader = 'ShoppRemoteAPIFile';

	/**
	 * API constructor
	 *
	 * @author Jonathan Davis
	 *
	 * @return void
	 **/
	public function __construct () {
		$this->path = SHOPP_REMOTE_APIS;

		$this->installed(); // Find modules
		$this->load(true);  // Load all

	}

} // END class ShoppAPILoader

class ShoppRemoteAPIFile extends ModuleFile {

	public function load () {
		include_once($this->file);
		$this->register();
	}

	public function register () {
		// Hook _context
		$api = $this->subpackage;
		if ( method_exists($api,'_register') ) call_user_func( array($api, '_register') );
	}

}