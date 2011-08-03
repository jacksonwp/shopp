<?php
/**
 * AdminFlow
 *
 * @author Jonathan Davis
 * @version 1.0
 * @copyright Ingenesis Limited, January 6, 2010
 * @package shopp
 * @subpackage admin
 **/

/**
 * AdminFlow
 *
 * @author Jonathan Davis
 * @package admin
 * @since 1.1
 **/
class AdminFlow extends FlowController {

	var $Pages = array();	// List of admin pages
	var $Menus = array();	// List of initialized WordPress menus
	var $Ajax = array();	// List of AJAX controllers
	var $MainMenu = false;
	var $Page = false;
	var $Menu = false;

	/**
	 * Initialize the capabilities, mapping to pages
	 *
	 * Capabilities						Role
	 * _______________________________________________
	 *
	 * shopp_settings					administrator
	 * shopp_settings_checkout
	 * shopp_settings_payments
	 * shopp_settings_shipping
	 * shopp_settings_taxes
	 * shopp_settings_presentation
	 * shopp_settings_system
	 * shopp_settings_update
	 * shopp_financials					shopp-merchant
	 * shopp_promotions
	 * shopp_products
	 * shopp_categories
	 * shopp_orders						shopp-csr
	 * shopp_customers
	 * shopp_menu
	 *
	 * @var $caps
	 **/
	var $caps = array(
		'main'=>'shopp_menu',
		'orders'=>'shopp_orders',
		'customers'=>'shopp_customers',
		'memberships'=>'shopp_products',
		'products'=>'shopp_products',
		'categories'=>'shopp_categories',
		'promotions'=>'shopp_promotions',
		'settings'=>'shopp_settings',
		'settings-preferences'=>'shopp_settings',
		'settings-payments'=>'shopp_settings_payments',
		'settings-shipping'=>'shopp_settings_shipping',
		'settings-taxes'=>'shopp_settings_taxes',
		'settings-pages'=>'shopp_settings_presentation',
		'settings-presentation'=>'shopp_settings_presentation',
		'settings-images'=>'shopp_settings_presentation',
		'settings-system'=>'shopp_settings_system'
	);

	/**
	 * Admin constructor
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function __construct () {
		parent::__construct();

		$this->legacyupdate();
		// Add Dashboard Widgets
		add_action('wp_dashboard_setup', array($this, 'dashboard'));
		add_action('admin_print_styles-index.php', array($this, 'dashboard_css'));
		add_action('admin_init', array($this, 'tinymce'));
		add_action('load-plugins.php',array($this, 'pluginspage'));
		add_action('switch_theme',array($this, 'themepath'));
		add_filter('favorite_actions', array($this, 'favorites'));
		add_filter('shopp_admin_boxhelp', array($this, 'keystatus'));
		add_action('load-update.php', array($this, 'admin_css'));
		add_action('admin_menu',array($this,'taxonomies'),20);
		add_action('load-nav-menus.php',array($this,'navmenus'));
		add_action('wp_setup_nav_menu_item',array($this,'navmenu_setup'));

		// Add the default Shopp pages
		$this->addpage('orders',__('Orders','Shopp'),'Service','Managing Orders');
		$this->addpage('customers',__('Customers','Shopp'),'Account','Managing Customers');
		$this->addpage('products',__('Products','Shopp'),'Warehouse','Editing a Product','products');
		$this->addpage('categories',__('Categories','Shopp'),'Categorize','Editing a Category','products');

		// $this->addpage('tags',__('Tags','Shopp'),'Categorize','Editing a Tag','products');
		$taxonomies = get_object_taxonomies(Product::$posttype, 'object');
		foreach ( $taxonomies as $t ) {
			if ($t->name == 'shopp_category') continue;
			$pagehook = ltrim($t->name,'shopp_');
			$this->addpage($pagehook,$t->labels->menu_name,'Categorize','Editing Taxonomies','products');
		}

		$this->addpage('promotions',__('Promotions','Shopp'),'Promote','Running Sales & Promotions','products');
		// Not yet... $this->addpage('memberships',__('Memberships','Shopp'),'Members','Memberships & Access','products');
		$this->addpage('settings',__('Setup','Shopp'),'Setup','General Settings','settings');
		$this->addpage('settings-payments',__('Payments','Shopp'),'Setup','Payments Settings',"settings");
		$this->addpage('settings-shipping',__('Shipping','Shopp'),'Setup','Shipping Settings',"settings");
		$this->addpage('settings-taxes',__('Taxes','Shopp'),'Setup','Taxes Settings',"settings");
		$this->addpage('settings-pages',__('Pages','Shopp'),'Setup','Page Settings',"settings");
		$this->addpage('settings-images',__('Images','Shopp'),'Setup','Image Settings',"settings");
		$this->addpage('settings-presentation',__('Presentation','Shopp'),'Setup','Presentation Settings',"settings");
		$this->addpage('settings-preferences',__('Preferences','Shopp'),'Setup','Store Preferences',"settings");
		$this->addpage('settings-system',__('System','Shopp'),'Setup','System Settings',"settings");

		// Action hook for adding custom third-party pages
		do_action('shopp_admin_menu');

		reset($this->Pages);
		$this->MainMenu = key($this->Pages);

		wp_enqueue_style('shopp.menu',SHOPP_ADMIN_URI.'/styles/menu.css',array(),SHOPP_VERSION,'screen');

		// Set the currently requested page and menu
		if (isset($_GET['page'])) $page = strtolower($_GET['page']);
		else return;
		if (isset($this->Pages[$page])) $this->Page = $this->Pages[$page];
		if (isset($this->Menus[$page])) $this->Menu = $this->Menus[$page];

	}

	/**
	 * Generates the Shopp admin menu
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function menus () {
		global $Shopp,$menu;

		$access = $this->caps['main'];
		if ($this->maintenance()) $access = 'manage_options';

		$this->topmenu('main','Shopp',$access,'orders',50);
		$this->topmenu('catalog',__('Catalog','Shopp'),$access,'products',50);
		$this->topmenu('setup',__('Setup','Shopp'),$access,'settings',50);

		// Add after the Shopp menus to avoid being purged by the duplicate separator check
		$menu[49] = array( '', 'read', 'separator-shopp', '', 'wp-menu-separator' );

		// Add menus to WordPress admin
		foreach ($this->Pages as $page) $this->addmenu($page);

		// Add admin JavaScript & CSS
		add_action('admin_enqueue_scripts', array($this, 'behaviors'),50);

		if ($this->maintenance()) return;

		// Add contextual help menus
		foreach ($this->Menus as $pagename => $item) $this->help($pagename,$item);
	}

	/**
	 * Registers a new page to the Shopp admin pages
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param string $name The internal reference name for the page
	 * @param string $label The label displayed in the WordPress admin menu
	 * @param string $controller The name of the controller to use for the page
	 * @param string $doc The title of the documentation article on docs.shopplugin.net
	 * @param string $parent The internal reference for the parent page
	 * @return void
	 **/
	function addpage ($name,$label,$controller,$doc=false,$parent=false) {
		$page = basename(SHOPP_PATH)."-$name";
		if (!empty($parent)) $parent = basename(SHOPP_PATH)."-$parent";
		$this->Pages[$page] = new ShoppAdminPage($name,$page,$label,$controller,$doc,$parent);
	}

	/**
	 * Adds a ShoppAdminPage entry to the Shopp admin menu
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 * @param mixed $page ShoppAdminPage object
	 **/
	function addmenu ($page) {
		global $Shopp;
		$name = $page->page;

		$controller = array(&$Shopp->Flow,'admin');
		if (shopp_setting('display_welcome') == "on" &&  empty($_POST['setup']))
			$controller = array($this,'welcome');
		if ($this->maintenance()) $controller = array($this,'reactivate');

		do_action('shopp_add_menu_'.$page->page);

		$capability = "none";
		if (isset($this->caps[$page->name]))
			$capability = $this->caps[$page->name];

		$taxonomies = get_object_taxonomies(Product::$posttype, 'names');
		if (in_array('shopp_'.$page->name,$taxonomies)) $capability = 'shopp_categories';

		$this->Menus[$page->page] = add_submenu_page(
			($page->parent)?$page->parent:$this->MainMenu,
			$page->label,
			$page->label,
			$capability,
			$name,
			$controller
		);

	}

	function topmenu ($name,$label,$access,$page,$position=50) {
		global $Shopp,$menu;

		do_action('shopp_add_topmenu_'.$page);

		while (isset($menu[$position])) $position++;

		$this->Menus[$page] = add_menu_page(
			$label,										// Page title
			$label,										// Menu title
			$access,									// Access level
			basename(SHOPP_PATH).'-'.$page,				// Page
			array(&$Shopp->Flow,'parse'),				// Handler
			SHOPP_ADMIN_URI.'/icons/clear.png',			// Icon
			$position									// Menu position
		);
	}

	/**
	 * Provide admin support for custom Shopp taxonomies
	 *
	 * @author Jonathan Davis
	 * @since 1.2
	 *
	 * @return void
	 **/
	function taxonomies () {
		global $menu,$submenu;
		if (!is_array($submenu)) return;

		$taxonomies = get_object_taxonomies(Product::$posttype);
		foreach ($submenu['shopp-products'] as &$submenus) {
			$taxonomy_name = str_replace('-','_',$submenus[2]);
			if (!in_array($taxonomy_name,$taxonomies)) continue;
			$submenus[2] = 'edit-tags.php?taxonomy='.$taxonomy_name;
			add_filter('manage_edit-'.$taxonomy_name.'_columns', array($this,'taxonomy_cols'));
			add_filter('manage_'.$taxonomy_name.'_custom_column', array($this,'taxonomy_product_column'), 10, 3);
		}

		add_action('admin_print_styles-edit-tags.php',array($this,'admin_css'));
		add_action('admin_head-edit-tags.php', array($this,'taxonomy_menu'));
	}

	function taxonomy_menu () {
		global $parent_file,$taxonomy;
		$taxonomies = get_object_taxonomies(Product::$posttype);
		if (in_array($taxonomy,$taxonomies)) $parent_file = 'shopp-products';
	}

	function taxonomy_cols ($cols) {
		return array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Name'),
			'description' => __('Description'),
			'slug' => __('Slug'),
			'products' => __('Products','Shopp')
		);
	}

	function taxonomy_product_column ($markup, $name, $term_id) {
		global $taxonomy;
		if ('products' != $name) return;
		$term = get_term($term_id,$taxonomy);
		return '<a href="admin.php?page=shopp-products&'.$taxonomy.'='.$term->slug.'">'.$term->count.'</a>';
	}

	/**
	 * Takes an internal page name reference and builds the full path name
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param string $page The internal reference name for the page
	 * @return string The fully qualified resource name for the admin page
	 **/
	function pagename ($page) {
		return basename(SHOPP_PATH)."-$page";
	}

	/**
	 * Gets the name of the controller for the current request or the specified page resource
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param string $page (optional) The fully qualified reference name for the page
	 * @return string|boolean The name of the controller or false if not available
	 **/
	function controller ($page=false) {
		if (!$page && isset($this->Page->controller)) return $this->Page->controller;
		if (isset($this->Pages[$page])) return $this->Pages[$page]->controller;
		return false;
	}

	/**
	 * Dynamically includes necessary JavaScript and stylesheets for the admin
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function behaviors () {
		global $Shopp,$wp_version,$hook_suffix;
		if (!in_array($hook_suffix,$this->Menus)) return;
		$this->admin_css();

		shopp_enqueue_script('shopp');
		add_action('shopp_print_scripts',array(&$Shopp,'settingsjs'),100);

		$settings = array_filter(array_keys($this->Pages),array($this,'get_settings_pages'));
		if (in_array($this->Page->page,$settings)) shopp_enqueue_script('settings');

	}

	/**
	 * Queues the admin stylesheets
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function admin_css () {

		global $taxonomy;
		if (isset($taxonomy)) { // Prevent loading styles if not on Shopp taxonomy editor
			$taxonomies = get_object_taxonomies(Product::$posttype);
			if (!in_array($taxonomy,$taxonomies)) return;
		}

		wp_enqueue_style('shopp.colorbox',SHOPP_ADMIN_URI.'/styles/colorbox.css',array(),SHOPP_VERSION,'screen');
		wp_enqueue_style('shopp.admin',SHOPP_ADMIN_URI.'/styles/admin.css',array(),SHOPP_VERSION,'screen');
	}

	/**
	 * Determines if a database schema upgrade is required
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function maintenance () {
		$db_version = intval(shopp_setting('db_version'));
		if ($db_version != DB::$version) return true;
		return false;
	}

	/**
	 * Adds contextually appropriate help information to interfaces
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function help ($pagename,$menu) {
		global $Shopp;
		if (!isset($this->Pages[$pagename])) return;
		$page = $this->Pages[$pagename];
		$url = SHOPP_DOCS.str_replace("+","_",urlencode($page->doc));
		$link = htmlspecialchars($page->doc);
		$content = '<a href="'.$url.'" target="_blank">'.$link.'</a>';

		$target = substr($menu,strrpos($menu,'-')+1);
		if ($target == "orders" || $target == "customers") {
			ob_start();
			include(SHOPP_PATH."/core/ui/help/$target.php");
			$help = ob_get_contents();
			ob_end_clean();
			$content .= $help;
		}

		add_contextual_help($menu,$content);
	}

	/**
	 * Returns a postbox help link to launch help screencasts
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param string $id The ID of the help resource
	 * @return string The anchor tag for the help link
	 **/
	function boxhelp ($id) {
		$helpurl = add_query_arg(array('src'=>'help','id'=>$id),admin_url('admin.php'));
		return apply_filters('shopp_admin_boxhelp','<a href="'.esc_url($helpurl).'" class="help"></a>');
	}

	/**
	 * Displays the welcome screen
	 *
	 * @return boolean
	 * @author Jonathan Davis
	 **/
	function welcome () {
		global $Shopp;
		if (shopp_setting('display_welcome') == "on" && empty($_POST['setup'])) {
			include(SHOPP_ADMIN_PATH."/help/welcome.php");
			return true;
		}
		return false;
	}

	/**
	 * Displays the re-activate screen
	 *
	 * @return boolean
	 * @author Jonathan Davis
	 **/
	function reactivate () {
		global $Shopp;
		include(SHOPP_ADMIN_PATH."/help/reactivate.php");
	}

	/**
	 * Adds a 'New Product' shortcut to the WordPress admin favorites menu
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param array $actions List of actions in the menu
	 * @return array Modified actions list
	 **/
	function favorites ($actions) {
		$key = esc_url(add_query_arg(array('page'=>$this->pagename('products'),'id'=>'new'),'admin.php'));
	    $actions[$key] = array(__('New Product','Shopp'),8);
		return $actions;
	}

	/**
	 * Initializes the Shopp dashboard widgets
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function dashboard () {
		$dashboard = shopp_setting('dashboard');
		if ( ! ( current_user_can('shopp_financials') && "on" == $dashboard ) ) return false;

		wp_add_dashboard_widget('dashboard_shopp_stats', __('Shopp Stats','Shopp'), array($this,'stats_widget'),
			array('all_link' => '','feed_link' => '','width' => 'half','height' => 'single')
		);

		wp_add_dashboard_widget('dashboard_shopp_orders', __('Shopp Orders','Shopp'), array($this,'orders_widget'),
			array('all_link' => 'admin.php?page='.$this->pagename('orders'),'feed_link' => '','width' => 'half','height' => 'single')
		);

		wp_add_dashboard_widget('dashboard_shopp_products', __('Shopp Products','Shopp'), array($this,'products_widget'),
			array('all_link' => 'admin.php?page='.$this->pagename('products'),'feed_link' => '','width' => 'half','height' => 'single')
		);

	}

	/**
	 * Loads the Shopp admin CSS on the WordPress dashboard for widget styles
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function dashboard_css () {
		wp_enqueue_style('shopp.dashboard',SHOPP_ADMIN_URI.'/styles/dashboard.css',array(),SHOPP_VERSION,'screen');
	}

	/**
	 * Dashboard Widgets
	 */
	/**
	 * Renders the order stats widget
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function stats_widget ($args=null) {
		global $Shopp;
		$db = DB::get();
		$defaults = array(
			'before_widget' => '',
			'before_title' => '',
			'widget_name' => '',
			'after_title' => '',
			'after_widget' => ''
		);
		if (!$args) $args = array();
		$args = array_merge($defaults,$args);
		if (!empty($args)) extract( $args, EXTR_SKIP );

		echo $before_widget;

		echo $before_title;
		echo $widget_name;
		echo $after_title;

		$purchasetable = DatabaseObject::tablename(Purchase::$table);

		$results = $db->query("SELECT count(id) AS orders, SUM(total) AS sales, AVG(total) AS average,
		 						SUM(IF(UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP()-(86400*30),1,0)) AS wkorders,
								SUM(IF(UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP()-(86400*30),total,0)) AS wksales,
								AVG(IF(UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP()-(86400*30),total,null)) AS wkavg
		 						FROM $purchasetable WHERE txnstatus='CHARGED'");

		$orderscreen = add_query_arg('page',$this->pagename('orders'),admin_url('admin.php'));
		echo '<div class="table"><table><tbody>';
		echo '<tr><th colspan="2">'.__('Last 30 Days','Shopp').'</th><th colspan="2">'.__('Lifetime','Shopp').'</th></tr>';

		echo '<tr><td class="amount"><a href="' . $orderscreen . '">' . (int)$results->wkorders . '</a></td><td>' . _n('Order', 'Orders', (int)$results->wkorders, 'Shopp') . '</td>';
		echo '<td class="amount"><a href="' . $orderscreen . '">' . (int)$results->orders . '</a></td><td>' . _n('Order', 'Orders', (int)$results->orders, 'Shopp') . '</td></tr>';

		echo '<tr><td class="amount"><a href="'.$orderscreen.'">'.money($results->wksales).'</a></td><td>'.__('Sales','Shopp').'</td>';
		echo '<td class="amount"><a href="'.$orderscreen.'">'.money($results->sales).'</a></td><td>'.__('Sales','Shopp').'</td></tr>';

		echo '<tr><td class="amount"><a href="'.$orderscreen.'">'.money($results->wkavg).'</a></td><td>'.__('Average Order','Shopp').'</td>';
		echo '<td class="amount"><a href="'.$orderscreen.'">'.money($results->average).'</a></td><td>'.__('Average Order','Shopp').'</td></tr>';

		echo '</tbody></table></div>';

		echo $after_widget;

	}

	/**
	 * Renders the recent orders dashboard widget
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function orders_widget ($args=null) {
		global $Shopp;
		$db = DB::get();
		$defaults = array(
			'before_widget' => '',
			'before_title' => '',
			'widget_name' => '',
			'after_title' => '',
			'after_widget' => ''
		);
		if (!$args) $args = array();
		$args = array_merge($defaults,$args);
		if (!empty($args)) extract( $args, EXTR_SKIP );
		$statusLabels = shopp_setting('order_status');

		echo $before_widget;

		echo $before_title;
		echo $widget_name;
		echo $after_title;

		$purchasetable = DatabaseObject::tablename(Purchase::$table);
		$purchasedtable = DatabaseObject::tablename(Purchased::$table);

		$Orders = $db->query("SELECT p.*,count(i.id) as items FROM $purchasetable AS p LEFT JOIN $purchasedtable AS i ON i.purchase=p.id GROUP BY i.purchase ORDER BY created DESC LIMIT 6",AS_ARRAY);

		if (!empty($Orders)) {
		echo '<table class="widefat">';
		echo '<tr><th scope="col">'.__('Name','Shopp').'</th><th scope="col">'.__('Date','Shopp').'</th><th scope="col" class="num">'.__('Items','Shopp').'</th><th scope="col" class="num">'.__('Total','Shopp').'</th><th scope="col" class="num">'.__('Status','Shopp').'</th></tr>';
		echo '<tbody id="orders" class="list orders">';
		$even = false;
		foreach ($Orders as $Order) {
			echo '<tr'.((!$even)?' class="alternate"':'').'>';
			$even = !$even;
			echo '<td><a class="row-title" href="'.add_query_arg(array('page'=>$this->pagename('orders'),'id'=>$Order->id),admin_url('admin.php')).'" title="View &quot;Order '.$Order->id.'&quot;">'.((empty($Order->firstname) && empty($Order->lastname))?'(no contact name)':$Order->firstname.' '.$Order->lastname).'</a></td>';
			echo '<td>'.date("Y/m/d",mktimestamp($Order->created)).'</td>';
			echo '<td class="num">'.$Order->items.'</td>';
			echo '<td class="num">'.money($Order->total).'</td>';
			echo '<td class="num">'.$statusLabels[$Order->status].'</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
		} else {
			echo '<p>'.__('No orders, yet.','Shopp').'</p>';
		}

		echo $after_widget;

	}

	/**
	 * Renders the bestselling products dashboard widget
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function products_widget ($args=null) {
		global $Shopp;

		$defaults = array(
			'before_widget' => '',
			'before_title' => '',
			'widget_name' => '',
			'after_title' => '',
			'after_widget' => ''
		);

		if (!$args) $args = array();
		$args = array_merge($defaults,$args);
		if (!empty($args)) extract( $args, EXTR_SKIP );

		echo $before_widget;

		echo $before_title;
		echo $widget_name;
		echo $after_title;

		$RecentBestsellers = new BestsellerProducts(array('range' => array(-30,0),'show'=>5));
		$RecentBestsellers->load();

		echo '<table><tbody><tr>';
		echo '<td><h4>'.__('Recent Bestsellers','Shopp').'</h4>';
		echo '<ul>';
		if (empty($RecentBestsellers->products)) echo '<li>'.__('Nothing has been sold, yet.','Shopp').'</li>';
		foreach ($RecentBestsellers->products as $product)
			echo '<li><a href="'.add_query_arg(array('page'=>$this->pagename('products'),'id'=>$product->id),admin_url('admin.php')).'">'.$product->name.'</a> ('.$product->sold.')</li>';
		echo '</ul></td>';

		$LifetimeBestsellers = new BestsellerProducts(array('show'=>5));
		$LifetimeBestsellers->load();
		echo '<td><h4>'.__('Lifetime Bestsellers','Shopp').'</h4>';
		echo '<ul>';
		if (empty($LifetimeBestsellers->products)) echo '<li>'.__('Nothing has been sold, yet.','Shopp').'</li>';
		foreach ($LifetimeBestsellers->products as $product)
			echo '<li><a href="'.add_query_arg(array('page'=>$this->pagename('products'),'id'=>$product->id),admin_url('admin.php')).'">'.$product->name.'</a>'.(isset($product->sold)?' ('.$product->sold.')':' (0)').'</li>';
		echo '</ul></td>';
		echo '</tr></tbody></table>';
		echo $after_widget;

	}

	/**
	 * Update the stored path to the activated theme
	 *
	 * Automatically updates the Shopp theme path setting when the
	 * a new theme is activated.
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function themepath () {
		shopp_set_setting('theme_templates',addslashes(sanitize_path(STYLESHEETPATH.'/'."shopp")));
	}

	/**
	 * Report the current status of the update key
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function keystatus ($_=true) {
		if (!Shopp::activated()) return false;
		return $_;
	}

	/**
	 * Helper callback filter to identify editor-related pages in the pages list
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param string $pagename The full page reference name
	 * @return boolean True if the page is identified as an editor-related page
	 **/
	function get_editor_pages ($pagenames) {
		$filter = '-edit';
		if (substr($pagenames,strlen($filter)*-1) == $filter) return true;
		else return false;
	}

	/**
	 * Helper callback filter to identify settings pages in the pages list
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param string $pagename The page's full reference name
	 * @return boolean True if the page is identified as a settings page
	 **/
	function get_settings_pages ($pagenames) {
		$filter = '-settings';
		if (strpos($pagenames,$filter) !== false) return true;
		else return false;
	}

	/**
	 * Initializes the Shopp TinyMCE plugin
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void Description...
	 **/
	function tinymce () {
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;

		$len = strlen(ABSPATH); $p = '';
		for($i = 0; $i < $len; $i++) $p .= 'x'.dechex(ord(substr(ABSPATH,$i,1))+$len);

		// Add TinyMCE buttons when using rich editor
		if ('true' == get_user_option('rich_editing')) {
			global $pagenow,$plugin_page;
			$pages = array('post.php', 'post-new.php', 'page.php', 'page-new.php');
			$editors = array('shopp-products','shopp-categories');
			if(!(in_array($pagenow, $pages) || (in_array($plugin_page, $editors) && !empty($_GET['id']))))
				return false;

			wp_enqueue_script('shopp-tinymce',admin_url('admin-ajax.php').'?action=shopp_tinymce',array());
			wp_localize_script('shopp-tinymce', 'ShoppDialog', array(
				'title' => __('Insert Product Category or Product', 'Shopp'),
				'desc' => __('Insert a product or category from Shopp...', 'Shopp'),
				'p' => $p
			));

			add_filter('mce_external_plugins', array($this,'mceplugin'),5);
			add_filter('mce_buttons', array($this,'mcebutton'),5);
		}
	}

	/**
	 * Adds the Shopp TinyMCE plugin to the list of loaded plugins
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @param array $plugins The current list of plugins to load
	 * @return array The updated list of plugins to laod
	 **/
	function mceplugin ($plugins) {
		// Add a changing query string to keep the TinyMCE plugin from being cached & breaking TinyMCE in Safari/Chrome
		$plugins['Shopp'] = SHOPP_ADMIN_URI.'/behaviors/tinymce/tinyshopp.js?ver='.time();
		return $plugins;
	}

	/**
	 * Adds the Shopp button to the TinyMCE editor
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @param array $buttons The current list of buttons in the editor
	 * @return array The updated list of buttons in the editor
	 **/
	function mcebutton ($buttons) {
		array_push($buttons, "|", "Shopp");
		return $buttons;
	}

	/**
	 * Handle auto-updates from Shopp 1.0
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function legacyupdate () {
		global $plugin_page;

		if ($plugin_page == 'shopp-settings-update'
			&& isset($_GET['updated']) && $_GET['updated'] == 'true') {
				wp_redirect(add_query_arg('page',$this->pagename('orders'),admin_url('admin.php')));
				exit();
		}
	}

	/**
	 * Suppress the standard WordPress plugin update message for the Shopp listing on the plugin page
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function pluginspage () {
		remove_action('after_plugin_row_'.SHOPP_PLUGINFILE,'wp_plugin_update_row');
	}

	function navmenus () {
		if (isset($_REQUEST['add-shopp-menu-item']) && isset($_REQUEST['menu-item'])) {
			$pages = Storefront::pages_settings();

			$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

			foreach ((array)$_REQUEST['menu-item'] as $key => $item) {
				if (!isset($item['menu-item-shopp-page'])) continue;

				$requested = $item['menu-item-shopp-page'];

				$menuitem = &$_REQUEST['menu-item'][$key];
				$menuitem['menu-item-db-id'] = 0;
				$menuitem['menu-item-object-id'] = $requested;
				$menuitem['menu-item-object'] = $requested;
				$menuitem['menu-item-type'] = 'shopp_page';
				$menuitem['menu-item-title'] = $pages[$requested]['title'];
			}


		}
		add_meta_box( 'add-shopp-pages', __('Catalog Pages'), array($this,'shoppage_meta_box'), 'nav-menus', 'side', 'low' );
	}

	function navmenu_setup ($menuitem) {
		if ('shopp_page' == $menuitem->type) {
			$menuitem->type_label = 'Shopp';
		}
		return $menuitem;
	}

	function shoppage_meta_box () {
		global $_nav_menu_placeholder, $nav_menu_selected_id;

		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		?>
		<br />
		<div class="shopp-pages-menu-item customlinkdiv" id="shopp-pages-menu-item">
			<div id="tabs-panel-shopp-pages" class="tabs-panel tabs-panel-active">

				<ul class="categorychecklist form-no-clear">

				<?php
					$pages = Storefront::pages_settings();
					foreach ($pages as $pagetype => $page):
						$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
				?>
					<li>
						<label class="menu-item-title">
						<input type="checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-shopp-page]" value="<?php echo $pagetype; ?>" class="menu-item-checkbox" /> <?php
							echo esc_html( $page['title'] );
						?></label>
						<input type="hidden" class="menu-item-db-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-db-id]" value="0" />
						<input type="hidden" class="menu-item-object-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="<?php echo $pagetype; ?>" />
						<input type="hidden" class="menu-item-object" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object]" value="<?php echo $pagetype; ?>" />
						<input type="hidden" class="menu-item-parent-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-parent-id]" value="0">
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="shopp_page" />
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php echo $page['title']; ?>" />

					</li>
				<?php endforeach; ?>
				</ul>

			</div>

			<p class="button-controls">
				<span class="list-controls">
					<a href="<?php
						echo esc_url(add_query_arg(
							array(
								'shopp-pages-menu-item' => 'all',
								'selectall' => 1,
							),
							remove_query_arg($removed_args)
						));
					?>#shopp-pages-menu-item" class="select-all"><?php _e('Select All'); ?></a>
				</span>

				<span class="add-to-menu">
					<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
					<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-shopp-menu-item" id="submit-shopp-pages-menu-item" />
				</span>
			</p>

		</div><!-- /.customlinkdiv -->
		<?php

	}

} // END class AdminFlow

/**
 * ShoppAdminPage class
 *
 * A property container for Shopp's admin page meta
 *
 * @author Jonathan Davis
 * @since 1.1
 * @package admin
 **/
class ShoppAdminPage {
	var $label = "";
	var $controller = "";
	var $doc = false;
	var $parent = false;

	function __construct ($name,$page,$label,$controller,$doc=false,$parent=false) {
		$this->name = $name;
		$this->page = $page;
		$this->label = $label;
		$this->controller = $controller;
		$this->doc = $doc;
		$this->parent = $parent;
	}

} // END class ShoppAdminPage


class ShoppUI {

	static function button ($type,$name,$options=array()) {
		$types = array(
			'add' => array('class' => 'add','imgalt' => '+', 'imgsrc' => '/add.png'),
			'delete' => array('class' => 'delete','imgalt' => '-','imgsrc' => '/delete.png')
		);
		if (isset($types[$type]))
			$options = array_merge($types[$type],$options);

		return '<button type="submit" name="'.$name.'"'.inputattrs($options).'><img src="'.SHOPP_ICONS_URI.$options['imgsrc'].'" alt="'.$options['imgalt'].'" width="16" height="16" /></button>';
	}

	static function template ($ui,$data=array()) {
		$ui = str_replace(array_keys($data),$data,$ui);
		return preg_replace('/\${[-\w]+}/','',$ui);
	}

} // END class ShoppUI
?>