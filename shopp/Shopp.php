<?php
/*
Plugin Name: Shopp
Version: 1.2b2r1
Description: Bolt-on ecommerce solution for WordPress
Plugin URI: http://shopplugin.net
Author: Ingenesis Limited
Author URI: http://ingenesis.net

	Portions created by Ingenesis Limited are Copyright © 2008-2011 by Ingenesis Limited

	This file is part of Shopp.

	Shopp is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Shopp is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Shopp.  If not, see <http://www.gnu.org/licenses/>.

*/

if (!defined('SHOPP_VERSION'))
	define('SHOPP_VERSION','1.2b2r1');
if (!defined('SHOPP_REVISION'))
	define('SHOPP_REVISION','$Rev$');
if (!defined('SHOPP_GATEWAY_USERAGENT'))
	define('SHOPP_GATEWAY_USERAGENT','WordPress Shopp Plugin/'.SHOPP_VERSION);
if (!defined('SHOPP_HOME'))
	define('SHOPP_HOME','https://shopplugin.net/');
if (!defined('SHOPP_CUSTOMERS'))
	define('SHOPP_CUSTOMERS','http://customers.shopplugin.net/');
if (!defined('SHOPP_DOCS'))
	define('SHOPP_DOCS','http://docs.shopplugin.net/');

require('core/legacy.php');

// Don't load Shopp if unsupported
if (SHOPP_UNSUPPORTED) return;

require('core/functions.php');

// Load core app helpers
require('core/Framework.php');
require('core/DB.php');
require('core/model/Settings.php');
require('core/model/Error.php');

// Load super controllers
require('core/flow/Flow.php');
require('core/flow/Storefront.php');
require('core/flow/Login.php');
require('core/flow/Scripts.php');

// Load frameworks & Shopp-managed data model objects
require('core/model/Modules.php');
require('core/model/Gateway.php');
require('core/model/Shipping.php');
require('core/model/API.php');
require('core/model/Lookup.php');
require('core/model/Shopping.php');

require('core/model/Meta.php');
require('core/model/Order.php');
require('core/model/Cart.php');
require('core/model/Asset.php');
require('core/model/Catalog.php');
require('core/model/Purchase.php');
require('core/model/Customer.php');

// Load public development API
require('api/core.php');
require('api/theme.php');
require('api/asset.php');
require('api/cart.php');
require('api/collection.php');
require('api/customer.php');
require('api/meta.php');
require('api/order.php');
require('api/settings.php');
require('api/product.php');

// Start up the core
$Shopp = new Shopp();
do_action('shopp_loaded');

/**
 * Shopp class
 *
 * @author Jonathan Davis
 * @package shopp
 * @since 1.0
 **/
class Shopp {

	var $Settings;			// Shopp settings registry
	var $Flow;				// Controller routing
	var $Catalog;			// The main catalog
	var $Category;			// Current category
	var $Product;			// Current product
	var $Purchase; 			// Currently requested order receipt
	var $Shopping; 			// The shopping session
	var $Errors;			// Error system
	var $Order;				// The current session Order
	var $Promotions;		// Active promotions registry
	var $Collections;		// Collections registry
	var $Gateways;			// Gateway modules
	var $Shipping;			// Shipping modules
	var $APIs;				// Loaded API modules
	var $Storage;			// Storage engine modules

	var $_debug;

	function __construct () {
		if (WP_DEBUG) {
			$this->_debug = new StdClass();
			if (function_exists('memory_get_peak_usage'))
				$this->_debug->memory = memory_get_peak_usage(true);
			if (function_exists('memory_get_usage'))
				$this->_debug->memory = memory_get_usage(true);
		}

		// Determine system and URI paths

		$path = sanitize_path(dirname(__FILE__));
		$file = basename(__FILE__);
		$directory = basename($path);

		$languages_path = array($directory,'lang');
		load_plugin_textdomain('Shopp',false,sanitize_path(join('/',$languages_path)));

		$uri = WP_PLUGIN_URL."/$directory";
		$wpadmin_url = admin_url();

		if ($this->secure = is_shopp_secure()) {
			$uri = str_replace('http://','https://',$uri);
			$wpadmin_url = str_replace('http://','https://',$wpadmin_url);
		}

		if (!defined('BR')) define('BR','<br />');

		// Overrideable config macros
		if (!defined('SHOPP_NOSSL')) define('SHOPP_NOSSL',false);
		if (!defined('SHOPP_PREPAYMENT_DOWNLOADS')) define('SHOPP_PREPAYMENT_DOWNLOADS',false);
		if (!defined('SHOPP_SESSION_TIMEOUT')) define('SHOPP_SESSION_TIMEOUT',7200);
		if (!defined('SHOPP_QUERY_DEBUG')) define('SHOPP_QUERY_DEBUG',false);
		if (!defined('SHOPP_GATEWAY_TIMEOUT')) define('SHOPP_GATEWAY_TIMEOUT',10);
		if (!defined('SHOPP_SHIPPING_TIMEOUT')) define('SHOPP_SHIPPING_TIMEOUT',10);
		if (!defined('SHOPP_TEMP_PATH')) define('SHOPP_TEMP_PATH',sys_get_temp_dir());
		if (!defined('SHOPP_NAMESPACE_TAXONOMIES')) define('SHOPP_NAMESPACE_TAXONOMIES',true);

		// Settings & Paths
		define('SHOPP_DEBUG',(shopp_setting('error_logging') == 2048));
		define('SHOPP_PATH',$path);
		define('SHOPP_DIR',$directory);
		define('SHOPP_PLUGINURI',$uri);
		define('SHOPP_WPADMIN_URL',$wpadmin_url);
		define('SHOPP_PLUGINFILE',"$directory/$file");

		define('SHOPP_ADMIN_DIR','/core/ui');
		define('SHOPP_ADMIN_PATH',SHOPP_PATH.SHOPP_ADMIN_DIR);
		define('SHOPP_ADMIN_URI',SHOPP_PLUGINURI.SHOPP_ADMIN_DIR);
		define('SHOPP_ICONS_URI',SHOPP_ADMIN_URI.'/icons');
		define('SHOPP_FLOW_PATH',SHOPP_PATH.'/core/flow');
		define('SHOPP_MODEL_PATH',SHOPP_PATH.'/core/model');
		define('SHOPP_GATEWAYS',SHOPP_PATH.'/gateways');
		define('SHOPP_SHIPPING',SHOPP_PATH.'/shipping');
		define('SHOPP_STORAGE',SHOPP_PATH.'/storage');
		define('SHOPP_THEME_APIS',SHOPP_PATH.'/api/theme');
		define('SHOPP_DBSCHEMA',SHOPP_MODEL_PATH.'/schema.sql');

		define('SHOPP_TEMPLATES',(shopp_setting('theme_templates') != 'off'
			&& is_dir(sanitize_path(get_stylesheet_directory().'/shopp')))?
					  sanitize_path(get_stylesheet_directory().'/shopp'):
					  SHOPP_PATH.'/templates');
		define('SHOPP_TEMPLATES_URI',(shopp_setting('theme_templates') != 'off'
			&& is_dir(sanitize_path(get_stylesheet_directory().'/shopp')))?
					  sanitize_path(get_bloginfo('stylesheet_directory').'/shopp'):
					  SHOPP_PLUGINURI.'/templates');

		define('SHOPP_PRETTYURLS',(get_option('permalink_structure') == '')?false:true);
		define('SHOPP_PERMALINKS',SHOPP_PRETTYURLS); // Deprecated

		// Error singletons
		ShoppErrors();
		ShoppErrorLogging();
		ShoppErrorNotification();

		// Initialize application control processing
		$this->Flow = new Flow();

		// Init old properties for legacy add-on module compatibility
		$this->Shopping = ShoppShopping();
		$this->Settings = ShoppSettings();

		add_action('init', array(&$this,'init'));

		// Core WP integration
		add_action('shopp_init', array(&$this,'pages'));
		add_action('shopp_init', array(&$this,'collections'));
		add_action('shopp_init', array(&$this,'taxonomies'));
		add_action('shopp_init', array(&$this,'products'),99);

		add_filter('rewrite_rules_array',array(&$this,'rewrites'));
		add_filter('query_vars', array(&$this,'queryvars'));

		// Theme integration
		add_action('widgets_init', array(&$this, 'widgets'));
		add_filter('wp_list_pages',array(&$this,'secure_links'));

		// Plugin management
        add_action('after_plugin_row_'.SHOPP_PLUGINFILE, array(&$this, 'status'),10,2);
        add_action('install_plugins_pre_plugin-information', array(&$this, 'changelog'));
        add_action('shopp_check_updates', array(&$this, 'updates'));

		if (!wp_next_scheduled('shopp_check_updates'))
			wp_schedule_event(time(),'twicedaily','shopp_check_updates');

	}

	/**
	 * Initializes the Shopp runtime environment
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function init () {
		$Shopping = ShoppShopping();

		$this->Order = ShoppingObject::__new('Order');
		$this->Promotions = ShoppingObject::__new('CartPromotions');
		$this->Gateways = new GatewayModules();
		$this->Shipping = new ShippingModules();
		$this->Storage = new StorageEngines();
		$this->APIs = new ShoppAPIModules();
		$this->Collections = array();

		if ( ! $Shopping->handlers) new ShoppError(__('The Cart session handlers could not be initialized because the session was started by the active theme or an active plugin before Shopp could establish its session handlers. The cart will not function.','Shopp'),'shopp_cart_handlers',SHOPP_ADMIN_ERR);
		if (SHOPP_DEBUG) new ShoppError('Session started '.str_repeat('-',64),'shopp_session_debug',SHOPP_DEBUG_ERR);

		global $pagenow;
		if (defined('WP_ADMIN')
			&& 'plugins.php' == $pagenow
			&& isset($_GET['action'])
			&& $_GET['action'] != 'deactivate') $this->updates();

		new Login();
		do_action('shopp_init');
	}

	/**
	 * Initializes theme widgets
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function widgets () {
		global $wp_version;
		include('core/ui/widgets/account.php');
		include('core/ui/widgets/cart.php');
		include('core/ui/widgets/categories.php');
		include('core/ui/widgets/section.php');
		include('core/ui/widgets/tagcloud.php');
		include('core/ui/widgets/facetedmenu.php');
		include('core/ui/widgets/product.php');
		include('core/ui/widgets/shoppers.php');
		include('core/ui/widgets/search.php');
	}

	function pages () {
		$var = "shopp_page"; $pages = array();
		$settings = Storefront::pages_settings();
		$catalog = array_shift($settings);
		foreach ($settings as $page) $pages[] = $page['slug'];
		add_rewrite_tag("%$var%", '('.join('|',$pages).')');
		add_permastruct($var, "{$catalog['slug']}/%$var%", false, EP_NONE);
	}

	function collections () {
		shopp_register_collection('CatalogProducts');
		shopp_register_collection('NewProducts');
		shopp_register_collection('FeaturedProducts');
		shopp_register_collection('OnSaleProducts');
		shopp_register_collection('BestsellerProducts');
		shopp_register_collection('SearchResults');
		shopp_register_collection('TagProducts');
		shopp_register_collection('RelatedProducts');
		shopp_register_collection('AlsoBoughtProducts');
		shopp_register_collection('ViewedProducts');
		shopp_register_collection('RandomProducts');
		shopp_register_collection('PromoProducts');
	}

	function taxonomies () {
		ProductTaxonomy::register('ProductCategory');
		ProductTaxonomy::register('ProductTag');
	}

	function products () {
		WPShoppObject::register('Product',Storefront::slug());
	}

	/**
	 * Adds Shopp-specific mod_rewrite rule for low-resource, speedy image server
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @param array $wp_rewrite_rules An array of existing WordPress rewrite rules
	 * @return array Rewrite rules
	 **/
	function rewrites ($wp_rewrite_rules) {
		$path = array(PLUGINDIR,SHOPP_DIR,'core');
		add_rewrite_rule('.*'.Storefront::slug().'/images/(\d+)/?\??(.*)$',urlencode(join('/',$path)).'/image.php?siid=$1&$2');
		return $wp_rewrite_rules;
	}

	/**
	 * Registers the query variables used by Shopp
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 * @version 1.2
	 *
	 * @param array $vars The current list of handled WordPress query vars
	 * @return array Augmented list of query vars including Shopp vars
	 **/
	function queryvars ($vars) {

		$vars[] = 's_pr';		// Shopp process parameter
		$vars[] = 's_rs';		// Shopp resource
		$vars[] = 's_iid';		// Shopp image id
		$vars[] = 's_cs';		// Catalog (search) flag
		$vars[] = 's_ac';		// Account process
		$vars[] = 's_cat';		// Category slug or id
		$vars[] = 's_tag';		// Tag slug
		$vars[] = 's_pid';		// Product ID
		$vars[] = 's_pd';		// Product slug
		$vars[] = 's_dl';		// Download key
		$vars[] = 's_so';		// Product sort order (product collections)
		$vars[] = 's_ff';		// Category filters

		$vars[] = 'shopp_page'; // Shopp pages

		return $vars;
	}

	/**
	 * Reset the shopping session
	 *
	 * Controls the cart to allocate a new session ID and transparently
	 * move existing session data to the new session ID.
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return boolean True on success
	 **/
	function resession ($session=false) {
		$Shopping = ShoppShopping();

		// commit current session
		session_write_close();
		$Shopping->handling(); // Workaround for PHP 5.2 bug #32330

		if ($session) { // loading session
			$Shopping->session = session_id($session); // session_id while session is closed
			$Shopping = ShoppShopping();
			$Shopping->init();
			session_start();
			return true;
		}

		session_start();
		session_regenerate_id(); // Generate new ID while session is started

		// Ensure we have the newest session ID
		$Shopping->session = session_id();

		// Commit the session and restart
		session_write_close();
		$Shopping->handling(); // Workaround for PHP 5.2 bug #32330
		session_start();

		do_action('shopp_reset_session'); // Deprecated
		do_action('shopp_resession');
		return true;

	}

	/**
	 * Provides the JavaScript environment with Shopp settings
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 * @todo Move Shopp::settingsjs predefined to Scripts.php
	 *
	 * @return void
	 **/
	function settingsjs () {
		$baseop = shopp_setting('base_operations');

		$currency = array();
		if (isset($baseop['currency'])
			&& isset($baseop['currency']['format'])
			&& isset($baseop['currency']['format']['decimals'])
			&& !empty($baseop['currency']['format']['decimals'])
		) {
			$settings = &$baseop['currency']['format'];
			$currency = array(
				// Currency formatting
				'cp' => $settings['cpos'],
				'c' =>  $settings['currency'],
				'p' =>  $settings['precision'],
				't' =>  $settings['thousands'],
				'd' =>  $settings['decimals']
			);

			if (isset($settings['grouping'])) {
				if (is_array($settings['grouping'])) $currency['g'] = join(',',$settings['grouping']);
				else $currency['g'] = $settings['grouping'];
			}
		}

		$base = array(
			'nocache' => is_shopp_page('account'),

			// Validation alerts
			'REQUIRED_FIELD' => __('Your %s is required.','Shopp'),
			'INVALID_EMAIL' => __('The e-mail address you provided does not appear to be a valid address.','Shopp'),
			'MIN_LENGTH' => __('The %s you entered is too short. It must be at least %d characters long.','Shopp'),
			'PASSWORD_MISMATCH' => __('The passwords you entered do not match. They must match in order to confirm you are correctly entering the password you want to use.','Shopp'),
			'REQUIRED_CHECKBOX' => __('%s must be checked before you can proceed.','Shopp')
		);

		$checkout = array();
		if (shopp_script_is('checkout')) {
			$checkout = array(
				'ajaxurl' => admin_url('admin-ajax.php'),

				// Alerts
				'LOGIN_NAME_REQUIRED' => __('You did not enter a login.','Shopp'),
				'LOGIN_PASSWORD_REQUIRED' => __('You did not enter a password to login with.','Shopp'),
			);
		}

		// Admin only
		if (defined('WP_ADMIN'))
			$base['UNSAVED_CHANGES_WARNING'] = __('There are unsaved changes that will be lost if you continue.','Shopp');

		$calendar = array();
		if (shopp_script_is('calendar')) {
			$calendar = array(
				// Month names
				'month_jan' => __('January','Shopp'),
				'month_feb' => __('February','Shopp'),
				'month_mar' => __('March','Shopp'),
				'month_apr' => __('April','Shopp'),
				'month_may' => __('May','Shopp'),
				'month_jun' => __('June','Shopp'),
				'month_jul' => __('July','Shopp'),
				'month_aug' => __('August','Shopp'),
				'month_sep' => __('September','Shopp'),
				'month_oct' => __('October','Shopp'),
				'month_nov' => __('November','Shopp'),
				'month_dec' => __('December','Shopp'),

				// Weekday names
				'weekday_sun' => __('Sun','Shopp'),
				'weekday_mon' => __('Mon','Shopp'),
				'weekday_tue' => __('Tue','Shopp'),
				'weekday_wed' => __('Wed','Shopp'),
				'weekday_thu' => __('Thu','Shopp'),
				'weekday_fri' => __('Fri','Shopp'),
				'weekday_sat' => __('Sat','Shopp')
			);
		}


		$defaults = apply_filters('shopp_js_settings',array_merge($currency,$base,$checkout,$calendar));
		shopp_localize_script('shopp','sjss',$defaults);
	}

	/**
	 * Filters the WP page list transforming unsecured URLs to secure URLs
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function secure_links ($linklist) {
		if (!$this->Gateways->secure) return $linklist;
		$hrefs = array(
			'checkout' => shoppurl(false,'checkout'),
			'account' => shoppurl(false,'account')
		);
		if (empty($this->Gateways->active)) return str_replace($hrefs['checkout'],shoppurl(false,'cart'),$linklist);

		foreach ($hrefs as $href) {
			$secure_href = str_replace("http://","https://",$href);
			$linklist = str_replace($href,$secure_href,$linklist);
		}
		return $linklist;
	}

	/**
	 * Communicates with the Shopp update service server
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param array $request (optional) A list of request variables to send
	 * @param array $data (optional) A list of data variables to send
	 * @param array $options (optional)
	 * @return string The response from the server
	 **/
	function callhome ($request=array(),$data=array(),$options=array()) {
		$query = http_build_query(array_merge(array('ver'=>'1.1'),$request),'','&');
		$data = http_build_query($data,'','&');

		$defaults = array(
			'method' => 'POST',
			'timeout' => 20,
			'redirection' => 7,
			'httpversion' => '1.0',
			'user-agent' => SHOPP_GATEWAY_USERAGENT.'; '.get_bloginfo( 'url' ),
			'blocking' => true,
			'headers' => array(),
			'cookies' => array(),
			'body' => $data,
			'compress' => false,
			'decompress' => true,
			'sslverify' => false
		);
		$params = array_merge($defaults,$options);

		$URL = SHOPP_HOME."?$query";

		$connection = new WP_Http();
		$result = $connection->request($URL,$params);

		if (200 != $response['code']) { // Fail, fallback to http instead
			$URL = str_replace('https://', 'http://', $URL);
			$connection = new WP_Http();
			$result = $connection->request($URL,$params);
		}

		if (is_wp_error($result)) {
			$errors = array(); foreach ($result->errors as $errname => $msgs) $errors[] = join(' ',$msgs);
			$errors = join(' ',$errors);

			new ShoppError($this->name.": ".Lookup::errors('gateway','fail')." $errors ".Lookup::errors('contact','admin')." (WP_HTTP)",'gateway_comm_err',SHOPP_COMM_ERR);
			return false;
		} elseif (empty($result) || !isset($result['response'])) {
			new ShoppError($this->name.": ".Lookup::errors('gateway','noresponse'),'gateway_comm_err',SHOPP_COMM_ERR);
			return false;
		} else extract($result);

		if (200 != $response['code']) {
			$error = Lookup::errors('gateway','http-'.$response['code']);
			if (empty($error)) $error = Lookup::errors('gateway','http-unkonwn');
			new ShoppError($this->name.": $error",'gateway_comm_err',SHOPP_COMM_ERR);
			return false;
		}

		return $body;

	}

	function key ($action,$key) {
		$actions = array('deactivate','activate');
		if (!in_array($action,$actions)) $action = reset($actions);
		$action = "$action-key";

		$request = array( 'ShoppServerRequest' => $action,'key' => $key,'site' => get_bloginfo('siteurl') );
		$response = Shopp::callhome($request);
		$result = json_decode($response);

		$result = apply_filters('shopp_update_key',$result);

		shopp_set_setting( 'updatekey',$result );

		return $response;
	}

	static function keysetting () {
		$data = base64_decode(shopp_setting('updatekey'));
		if (empty($data)) return false;
		return unpack(Lookup::keyformat(),$data);
	}

	static function activated () {
		$key = self::keysetting();
		return ('1' == $key['s']);
	}

	/**
	 * Checks for available updates
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return array List of available updates
	 **/
	function updates () {
		$updates = new StdClass();

		$addons = array_merge(
			$this->Gateways->checksums(),
			$this->Shipping->checksums(),
			$this->Storage->checksums()
		);

		$request = array("ShoppServerRequest" => "update-check");
		$data = array(
			'core' => SHOPP_VERSION,
			'addons' => join("-",$addons),
			'site' => get_bloginfo('url'),
			'wp' => get_bloginfo('version').(is_multisite()?' (multisite)':''),
			'mysql' => mysql_get_server_info(),
			'php' => phpversion(),
			'uploadmax' => ini_get('upload_max_filesize'),
			'postmax' => ini_get('post_max_size'),
			'server' => $_SERVER['SERVER_SOFTWARE'],
			'agent' => $_SERVER['HTTP_USER_AGENT']
		);

		$response = $this->callhome($request,$data);
		if ($response == '-1') return; // Bad response, bail
		$response = unserialize($response);

		unset($updates->response);

		if (isset($response->addons)) {
			$updates->response[SHOPP_PLUGINFILE.'/addons'] = $response->addons;
			unset($response->addons);
		}

		if (isset($response->id))
			$updates->response[SHOPP_PLUGINFILE] = $response;

		if (function_exists('get_site_transient')) $plugin_updates = get_site_transient('update_plugins');
		else $plugin_updates = get_transient('update_plugins');

		if (isset($updates->response)) {
			shopp_set_setting('updates',$updates);

			// Add Shopp to the WP plugin update notification count
			$plugin_updates->response[SHOPP_PLUGINFILE] = true;

		} else unset($plugin_updates->response[SHOPP_PLUGINFILE]); // No updates, remove Shopp from the plugin update count

		if (function_exists('set_site_transient')) set_site_transient('update_plugins',$plugin_updates);
		else set_transient('update_plugins',$plugin_updates);

		return $updates;
	}

	/**
	 * Loads the change log for an available update
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function changelog () {
		if($_REQUEST["plugin"] != "shopp") return;

		$request = array("ShoppServerRequest" => "changelog");
		$data = array();
		$response = $this->callhome($request,$data);

		echo '<html><head>';
		echo '<link rel="stylesheet" href="'.admin_url().'/css/install.css" type="text/css" />';
		echo '<link rel="stylesheet" href="'.SHOPP_ADMIN_URI.'/styles/admin.css" type="text/css" />';
		echo '</head>';
		echo '<body id="error-page" class="shopp-update">';
		echo $response;
		echo "</body>";
		echo '</html>';
		exit();
	}

	/**
	 * Reports on the availability of new updates and the update key
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function status () {
		$updates = shopp_setting('updates');
		$keysetting = Shopp::keysetting();
		$key = $keysetting['k'];

		$activated = ('1' == $keysetting['s']);
		$core = isset($updates->response[SHOPP_PLUGINFILE])?$updates->response[SHOPP_PLUGINFILE]:false;
		$addons = isset($updates->response[SHOPP_PLUGINFILE.'/addons'])?$updates->response[SHOPP_PLUGINFILE.'/addons']:false;

		if (!empty($core)	// Core update available
				&& isset($core->new_version)	// New version info available
				&& version_compare($core->new_version,SHOPP_VERSION,'>') // New version is greater than current version
			) {
			$plugin_name = 'Shopp';
			$details_url = admin_url('plugin-install.php?tab=plugin-information&plugin=' . $core->slug . '&TB_iframe=true&width=600&height=800');
			$update_url = wp_nonce_url('update.php?action=shopp&plugin='.SHOPP_PLUGINFILE,'upgrade-plugin_shopp');

			if (!$activated) { // Key not active
				$update_url = SHOPP_HOME."store/";
				$message = sprintf(__('There is a new version of %1$s available, but your %1$s key has not been activated. No automatic upgrade available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a> or <a href="%4$s">purchase a Shopp key</a> to get access to automatic updates and official support services.','Shopp'),$plugin_name,$details_url,esc_attr($plugin_name),$core->new_version,$update_url);
				shopp_set_setting('updates',false);
			} else $message = sprintf(__('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a> or <a href="%5$s">upgrade automatically</a>.'),$plugin_name,$details_url,esc_attr($plugin_name),$core->new_version,$update_url);

			echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">'.$message.'</div></td></tr>';

			return;
		}

		if (!$activated) { // No update availableKey not active
			$message = sprintf(__('Your Shopp key has not been activated. Feel free to <a href="%1$s">purchase a Shopp key</a> to get access to automatic updates and official support services.','Shopp'),SHOPP_HOME."store/");
			echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">'.$message.'</div></td></tr>';
			shopp_set_setting('updates',false);
			return;
		}

        if ($addons) {
			// Addon update messages
			foreach ($addons as $addon) {
				$message = sprintf(__('There is a new version of the %1$s add-on available. <a href="%2$s">Upgrade automatically</a> to version %3$s','Shopp'),$addon->name,wp_nonce_url('update.php?action=shopp&addon=' . $addon->slug.'&type='.$addon->type, 'upgrade-shopp-addon_' . $addon->slug),$addon->new_version);
				echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">'.$message.'</div></td></tr>';

			}
		}

	}

	/**
	 * Detect if this Shopp installation needs maintenance
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function maintenance () {
		// Settings unavailable
		if (!ShoppSettings()->available() || !shopp_setting('shopp_setup') != "completed")
			return false;

		shopp_set_setting('maintenance','on');
		return true;
	}

} // END class Shopp
?>