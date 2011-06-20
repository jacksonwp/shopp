<?php
/**
 * Setup
 *
 * Flow controller for settings management
 *
 * @author Jonathan Davis
 * @version 1.0
 * @copyright Ingenesis Limited, January 6, 2010
 * @package shopp
 * @subpackage shopp
 **/

/**
 * Setup
 *
 * @package shopp
 * @author Jonathan Davis
 **/
class Setup extends AdminController {

	var $screen = false;
	var $url;

	/**
	 * Setup constructor
	 *
	 * @return void
	 * @author Jonathan Davis
	 **/
	function __construct () {
		parent::__construct();
		$Settings = ShoppSettings();

		$this->url = add_query_arg(array('page'=>esc_attr($_GET['page'])),admin_url('admin.php'));
		$pages = explode("-",$_GET['page']);
		$this->screen = end($pages);
		switch ($this->screen) {
			case "checkout":
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('status-labels');
				shopp_localize_script( 'status-labels', '$sl', array(
					'prompt' => __('Are you sure you want to remove this order status label?','Shopp'),
				));
				break;
			case "taxes":
				wp_enqueue_script("suggest");
				shopp_enqueue_script('ocupload');
				shopp_enqueue_script('taxes');
				break;
			case "system":
				shopp_enqueue_script('colorbox');
				break;
			case "pages":
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('pages-settings');
				$this->pages_ui();
				break;
			case "images":
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('image-settings');
				shopp_localize_script( 'image-settings', '$is', array(
					'confirm' => __('Are you sure you want to remove this image preset?','Shopp'),
				));
				$this->images_ui();
				break;
			case "payments":
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('payments-settings');
				shopp_localize_script( 'payments-settings', '$ps', array(
					'confirm' => __('Are you sure you want to remove this payment system?','Shopp'),
				));

				$this->payments_ui();
				break;
			case "shipping":
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('shipping-settings');
				shopp_localize_script( 'shipping-settings', '$ps', array(
					'confirm' => __('Are you sure you want to remove this shipping rate?','Shopp'),
				));
				$this->subscreens = array(
					'rates' => __('Rates','Shopp'),
					'settings' => __('Settings','Shopp')
				);

				if ('on' == $Settings->get('shipping'))
					$this->shipping_ui();
				break;
			case "settings":
				shopp_enqueue_script('setup');

				$customer_service = " ".sprintf(__('Contact <a href="%s">customer service</a>.','Shopp'),SHOPP_CUSTOMERS);

				$this->keystatus = array(
					'ks_inactive' => sprintf(__('Activate your Shopp access key for automatic updates and official support services. If you don\'t have a Shopp key, feel free to support the project by <a href="%s">purchasing a key from shopplugin.net</a>.','Shopp'),SHOPP_HOME.'store/'),
					'k_000' => __('The server could not be reached because of a connection problem.','Shopp'),
					'ks_1' => __('An unkown error occurred.','Shopp'),
					'ks0' => __('This site has been deactivated.','Shopp'),
					'ks1' => __('This site has been activated.','Shopp'),
					'ks_100' => __('An unknown activation error occurred.','Shopp').$customer_service,
					'ks_101' => __('The key provided is not valid.','Shopp').$customer_service,
					'ks_102' => __('This site is not valid to activate the key.','Shopp').$customer_service,
					'ks_103' => __('The key provided could not be validated by shopplugin.net.','Shopp').$customer_service,
					'ks_104' => __('The key provided is already active on another site.','Shopp').$customer_service,
					'ks_200' => __('An unkown deactivation error occurred.','Shopp').$customer_service,
					'ks_201' => __('The key provided is not valid.','Shopp').$customer_service,
					'ks_202' => __('The site is not valid to be able to deactivate the key.','Shopp').$customer_service,
					'ks_203' => __('The key provided could not be validated by shopplugin.net.','Shopp').$customer_service
				);

				$l10n = array(
					'activate_button' => __('Activate Key','Shopp'),
					'deactivate_button' => __('De-activate Key','Shopp'),
					'connecting' => __('Connecting','Shopp')

				);
				$l10n = array_merge($l10n,$this->keystatus);
				shopp_localize_script( 'setup', '$sl', $l10n);
				break;
		}

	}

	/**
	 * Parses settings interface requests
	 *
	 * @return void
	 * @author Jonathan Davis
	 **/
	function admin () {
		switch($this->screen) {
			case "catalog": 		$this->catalog(); break;
			case "cart": 			$this->cart(); break;
			case "checkout": 		$this->checkout(); break;
			case "payments": 		$this->payments(); break;
			case "shipping": 		$this->shipping(); break;
			case "taxes": 			$this->taxes(); break;
			case "pages":			$this->pages(); break;
			case "presentation":	$this->presentation(); break;
			case "images":			$this->images(); break;
			case "system":			$this->system(); break;
			case "update":			$this->update(); break;
			default: 				$this->general();
		}
	}

	/**
	 * Displays the General Settings screen and processes updates
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function general () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$Settings = ShoppSettings();

		// Welcome screen handling
		if (!empty($_POST['setup'])) {
			$_POST['settings']['display_welcome'] = "off";
			$this->settings_save();
		}

		$country = (isset($_POST['settings']))?$_POST['settings']['base_operations']['country']:'';
		$countries = array();
		$countrydata = Lookup::countries();
		foreach ($countrydata as $iso => $c) {
			if (isset($_POST['settings']) && $_POST['settings']['base_operations']['country'] == $iso)
				$base_region = $c['region'];
			$countries[$iso] = $c['name'];
		}

		// Key activation
		if (!empty($_POST['activation'])) {
			check_admin_referer('shopp-settings-activation');
			$sitekey = Shopp::keysetting();
			$key = $_POST['updatekey'];
			if ($key == str_repeat('0',40)) $key = $sitekey['k'];
			Shopp::key($_POST['activation'],$key);
		}

		$sitekey = Shopp::keysetting();
		$activated = Shopp::activated();
		$key = $sitekey['k'];
		$status = $sitekey['s'];

		$type = "text";
		$action = 'activate';
		$button = __('Activate Key','Shopp');

		if ($activated) {
			$button = __('De-activate Key','Shopp');
			$action = 'deactivate';
			$type = "password";
			$key = str_repeat('0',strlen($key));
			$keystatus = $this->keystatus['ks1'];
		} else {
			if (str_repeat('0',40) == $key) $key = '';
		}

		$status_class = ($status < 0)?'activating':'';
		$keystatus = '';
		if (empty($key)) $keystatus = $this->keystatus['ks_inactive'];
		if (!empty($_POST['activation'])) $keystatus = $this->keystatus['ks'.str_replace('-','_',$status)];

		// Save settings
		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-general');
			$vat_countries = Lookup::vat_countries();
			$zone = $_POST['settings']['base_operations']['zone'];
			$_POST['settings']['base_operations'] = $countrydata[$_POST['settings']['base_operations']['country']];
			$_POST['settings']['base_operations']['country'] = $country;
			$_POST['settings']['base_operations']['zone'] = $zone;
			$_POST['settings']['base_operations']['currency']['format'] =
				scan_money_format($_POST['settings']['base_operations']['currency']['format']);
			if (in_array($_POST['settings']['base_operations']['country'],$vat_countries))
				$_POST['settings']['base_operations']['vat'] = true;
			else $_POST['settings']['base_operations']['vat'] = false;

			if (!isset($_POST['settings']['target_markets']))
				asort($_POST['settings']['target_markets']);

			$this->settings_save();
			$updated = __('Shopp settings saved.', 'Shopp');
		}

		$operations = $Settings->get('base_operations');
		$zones = Lookup::country_zones();
		if (isset($zones[$operations['country']]))
			$zones = $zones[$operations['country']];

		$targets = $Settings->get('target_markets');
		if (!$targets) $targets = array();

		include(SHOPP_ADMIN_PATH."/settings/settings.php");
	}

	function presentation () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_presentation')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$builtin_path = SHOPP_PATH.'/templates';
		$theme_path = sanitize_path(STYLESHEETPATH.'/shopp');

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-presentation');
			$updated = __('Shopp presentation settings saved.','Shopp');

			if (isset($_POST['settings']['theme_templates'])
				&& $_POST['settings']['theme_templates'] == "on"
				&& !is_dir($theme_path)) {
					$_POST['settings']['theme_templates'] = "off";
					$updated = __('Shopp theme templates can\'t be used because they don\'t exist.','Shopp');
			}

			if (empty($_POST['settings']['catalog_pagination']))
				$_POST['settings']['catalog_pagination'] = 0;
			$this->settings_save();
		}


		// Copy templates to the current WordPress theme
		if (!empty($_POST['install'])) {
			check_admin_referer('shopp-settings-presentation');
			copy_shopp_templates($builtin_path,$theme_path);
		}

		$status = "available";
		if (!is_dir($theme_path)) $status = "directory";
		else {
			if (!is_writable($theme_path)) $status = "permissions";
			else {
				$builtin = array_filter(scandir($builtin_path),"filter_dotfiles");
				$theme = array_filter(scandir($theme_path),"filter_dotfiles");
				if (empty($theme)) $status = "ready";
				else if (array_diff($builtin,$theme)) $status = "incomplete";
			}
		}

		$category_views = array("grid" => __('Grid','Shopp'),"list" => __('List','Shopp'));
		$row_products = array(2,3,4,5,6,7);
		$productOrderOptions = ProductCategory::sortoptions();
		$productOrderOptions['custom'] = __('Custom','Shopp');

		$orderOptions = array("ASC" => __('Order','Shopp'),
							  "DESC" => __('Reverse Order','Shopp'),
							  "RAND" => __('Shuffle','Shopp'));

		$orderBy = array("sortorder" => __('Custom arrangement','Shopp'),
						 "name" => __('File name','Shopp'),
						 "created" => __('Upload date','Shopp'));


		include(SHOPP_ADMIN_PATH."/settings/presentation.php");
	}

	function checkout () {
		global $Shopp;

		$Settings = ShoppSettings();
		$db =& DB::get();
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_checkout')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$purchasetable = DatabaseObject::tablename(Purchase::$table);
		$next = $db->query("SELECT IF ((MAX(id)) > 0,(MAX(id)+1),1) AS id FROM $purchasetable LIMIT 1");
		$next_setting = $Settings->get('next_order_id');

		if ($next->id > $next_setting) $next_setting = $next->id;

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-checkout');

			$next_order_id = $_POST['settings']['next_order_id'] = intval($_POST['settings']['next_order_id']);

			if ($next_order_id >= $next->id) {
				if ($db->query("ALTER TABLE $purchasetable AUTO_INCREMENT=".$db->escape($next_order_id)))
					$next_setting = $next_order_id;
			}


			$this->settings_save();
			$updated = __('Shopp checkout settings saved.','Shopp');
		}

		$downloads = array("1","2","3","5","10","15","25","100");
		$promolimit = array("1","2","3","4","5","6","7","8","9","10","15","20","25");
		$time = array(
			'1800' => __('30 minutes','Shopp'),
			'3600' => __('1 hour','Shopp'),
			'7200' => __('2 hours','Shopp'),
			'10800' => __('3 hours','Shopp'),
			'21600' => __('6 hours','Shopp'),
			'43200' => __('12 hours','Shopp'),
			'86400' => __('1 day','Shopp'),
			'172800' => __('2 days','Shopp'),
			'259200' => __('3 days','Shopp'),
			'604800' => __('1 week','Shopp'),
			'2678400' => __('1 month','Shopp'),
			'7952400' => __('3 months','Shopp'),
			'15901200' => __('6 months','Shopp'),
			'31536000' => __('1 year','Shopp'),
			);

		$statusLabels = $Settings->get('order_status');

		include(SHOPP_ADMIN_PATH."/settings/checkout.php");
	}

	/**
	 * Renders the shipping settings screen and processes updates
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function shipping () {

		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_shipping')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));


		global $Shopp;
		$Settings = ShoppSettings();

		$sub = 'settings';
		if ('on' == $Settings->get('shipping')) $sub = 'rates';
		if ( isset($_GET['sub']) && in_array( $_GET['sub'],array_keys($this->subscreens) ) )
			$sub = $_GET['sub'];


		if (!empty($_POST['save']) && empty($_POST['module']) ) {
			check_admin_referer('shopp-settings-shipping');

			$_POST['settings']['order_shipfee'] = floatvalue($_POST['settings']['order_shipfee']);

	 		$this->settings_save();
			$updated = __('Shipping settings saved.','Shopp');
		}

		// Handle ship rates UI
		if ('rates' == $sub && 'on' == $Settings->get('shipping')) return $this->shiprates();

		$base = $Settings->get('base_operations');
		$regions = Lookup::regions();
		$region = $regions[$base['region']];
		$useRegions = $Settings->get('shipping_regions');

		$areas = Lookup::country_areas();
		if (is_array($areas[$base['country']]) && $useRegions == "on")
			$areas = array_keys($areas[$base['country']]);
		else $areas = array($base['country'] => $base['name']);
		unset($countries,$regions);

		$rates = $Settings->get('shipping_rates');
		if (!empty($rates)) ksort($rates);

		$lowstock = $Settings->get('lowstock_level');
		if (empty($lowstock)) $lowstock = 0;

		include(SHOPP_ADMIN_PATH."/settings/shipping.php");
	}

	function shiprates () {
		global $Shopp;
		$Settings = ShoppSettings();
		$Shipping = $Shopp->Shipping;
		$Shipping->settings(); // Load all installed shipping modules for settings UIs

		$methods = $Shopp->Shipping->methods;

		$active = $Settings->get('active_shipping');
		if (!$active) $active = array();

		if (!empty($_GET['delete'])) {
			check_admin_referer('shopp_delete_shiprate');
			$delete = $_GET['delete'];
			$index = false;
			if (strpos($delete,'-') !== false)
				list($delete,$index) = explode('-',$delete);

			if (array_key_exists($delete,$active))  {
				if (is_array($active[$delete])) {
					if (array_key_exists($index,$active[$delete])) {
						unset($active[$delete][$index]);
						if (empty($active[$delete])) unset($active[$delete]);
					}
				} else unset($active[$delete]);
				$updated = __('Shipping method setting removed.','Shopp');

				$Settings->save('active_shipping',$active);
			}
		}


		if (isset($_POST['module'])) {
			check_admin_referer('shopp-settings-shiprate');

			$setting = false;
			$module = isset($_POST['module'])?$_POST['module']:false;
			$id = isset($_POST['id'])?$_POST['id']:false;

			if ($id == $module) {
				if (isset($_POST['settings'])) $this->settings_save();
				/** Save shipping service settings **/
				$active[$module] = true;
				$Settings->save('active_shipping',$active);
				$updated = __('Shipping settings saved.','Shopp');
				// Cancel editing if saving
				if (isset($_POST['save'])) unset($_REQUEST['id']);

				$Errors = &ShoppErrors();
				do_action('shopp_verify_shipping_services');

				if ($Errors->exist()) {
					// Get all addon related errors
					$failures = $Errors->level(SHOPP_ADDON_ERR);
					if (!empty($failures)) {
						$updated = __('Shipping settings saved but there were errors: ','Shopp');
						foreach ($failures as $error)
							$updated .= '<p>'.$error->message(true,true).'</p>';
					}
				}

			} else {
				/** Save shipping calculator settings **/

				$setting = $_POST['id'];
				if (empty($setting)) { // Determine next available setting ID
					$index = 0;
					if (is_array($active[$module])) $index = count($active[$module]);
					$setting = "$module-$index";
				}

				// Cancel editing if saving
				if (isset($_POST['save'])) unset($_REQUEST['id']);

				list($setting_module,$id) = explode('-',$setting);

				// Prevent fishy stuff from happening
				if ($module != $setting_module) $module = false;

				// Save shipping calculator settings
				$Shipper = $Shipping->get($module);
				if ($Shipper && isset($_POST[$module])) {
					$Shipper->setting($id);

					$_POST[$module]['label'] = stripslashes($_POST[$module]['label']);

					// Sterilize $values
					foreach ($_POST[$module]['table'] as $i => &$row) {

						if (isset($row['rate'])) $row['rate'] = floatvalue($row['rate']);
						if (!isset($row['tiers'])) continue;

						foreach ($row['tiers'] as &$tier) {
							if (isset($tier['rate'])) $tier['rate'] = floatvalue($tier['rate']);
						}

					}

					$Settings->save($Shipper->setting,$_POST[$module]);
					if (!array_key_exists($module,$active)) $active[$module] = array();
					$active[$module][(int)$id] = true;
					$Settings->save('active_shipping',$active);
					$updated = __('Shipping settings saved.','Shopp');
				}

			}
		}


		$Shipping->ui(); // Setup setting UIs
		$installed = array();
		$shiprates = array();	// Registry for activated shipping rate modules
		$settings = array();	// Registry of loaded settings for table-based shipping rates for JS

		foreach ($Shipping->active as $name => $module) {
			$default_name = strtolower($name);
			$fullname = $module->methods();
			$installed[$name] = $fullname;

			if ($module->ui->tables) {
				$defaults[$default_name] = $module->ui->settings();
				$defaults[$default_name]['name'] = $fullname;
				$defaults[$default_name]['label'] = __('Shipping Method','Shopp');
			}

			if (array_key_exists($name,$active)) $ModuleSetting = $active[$name];
			else continue; // Not an activated shipping module, go to the next one

			// Setup shipping service shipping rate entries and settings
			if (!is_array($ModuleSetting)) {
				$shiprates[$name] = $name;
				continue;
			}

			// Setup shipping calcualtor shipping rate entires and settings
			foreach ($ModuleSetting as $id => $m) {
				$setting = "$name-$id";
				$shiprates[$setting] = $name;

				$settings[$setting] = $Settings->get($setting);
				$settings[$setting]['id'] = $setting;
				$settings[$setting] = array_merge($defaults[$default_name],$settings[$setting]);
			}

		}

		if ( isset($_REQUEST['id']) ) {
			$edit = $_REQUEST['id'];
			$id = false;
			if (strpos($edit,'-') !== false)
				list($module,$id) = explode('-',$edit);
			else $module = $edit;
			if (isset($Shipping->active[ $module ]) ) {
				$Shipper = $Shipping->get($module);
				if (!$Shipper->singular) {
					$Shipper->setting($id);
					$Shipper->initui($Shipping->modules[$module]->name); // Re-init setting UI with loaded settings
				}
				$editor = $Shipper->ui();
			}

		}

		asort($installed);

		$countrydata = Lookup::countries();
		$countries = $regionmap = $postcodes = array();
		$postcodedata = Lookup::postcodes();
		foreach ($countrydata as $code => $country) {
			$countries[$code] = $country['name'];
			if ( !isset($regionmap[ $country['region'] ]) ) $regionmap[ $country['region'] ] = array();
			$regionmap[ $country['region'] ][] = $code;
			if ( isset($postcodedata[$code])) {
				if ( !isset($postcodes[ $code ]) ) $postcodes[ $code ] = array();
				$postcodes[$code] = true;
			}
		}
		unset($countrydata);
		unset($postcodedata);


		$lookup = array(
			'regions' => array_merge(array('*' => __('Anywhere','Shopp')),Lookup::regions()),
			'regionmap' => $regionmap,
			'countries' => $countries,
			'areas' => Lookup::country_areas(),
			'zones' => Lookup::country_zones(),
			'postcodes' => $postcodes
		);

		$ShippingTemplates = new TemplateShippingUI();
		add_action('shopp_shipping_module_settings',array($Shipping,'templates'));
		include(SHOPP_ADMIN_PATH."/settings/shiprates.php");

	}

	function shipping_menu () {
		$Settings = ShoppSettings();
		if ('off' == $Settings->get('shipping')) return;
		?>
		<ul class="subsubsub">
			<?php $i = 0; foreach ($this->subscreens as $screen => $label):  $url = add_query_arg(array('sub'=>$screen),$this->url); ?>
				<li><a href="<?php echo esc_url($url); ?>"<?php if ($_GET['page'] == $page) echo ' class="current"'; ?>><?php echo $label; ?></a><?php if (count($this->subscreens)-1!=$i++): ?> | <?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
		<br class="clear" />
		<?php
	}

	function shipping_ui () {
		register_column_headers('shopp_page_shopp-settings-shipping', array(
			'cb'=>'<input type="checkbox" />',
			'name'=>__('Name','Shopp'),
			'type'=>__('Type','Shopp'),
			'destinations'=>__('Destinations','Shopp')
		));
	}


	function taxes () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_taxes')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-taxes');
			$this->settings_save();
			$updated = __('Shopp taxes settings saved.','Shopp');
		}

		$rates = $this->Settings->get('taxrates');
		$base = $this->Settings->get('base_operations');

		$countries = array_merge(array('*' => __('All Markets','Shopp')),
			$this->Settings->get('target_markets'));

		$zones = Lookup::country_zones();

		include(SHOPP_ADMIN_PATH."/settings/taxes.php");
	}

	function payments () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_payments')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		global $Shopp;
		$Gateways = $Shopp->Gateways;
		$Settings = ShoppSettings();

	 	$active_gateways = $Settings->get('active_gateways');
		if (!$active_gateways) $gateways = array();
		else $gateways = explode(',',$active_gateways);
		$Gateways->settings();	// Load all installed gateways for settings UIs

		if (!empty($_GET['delete'])) {
			$delete = $_GET['delete'];
			check_admin_referer('shopp_delete_gateway');
			if (in_array($delete,$gateways))  {
				$position = array_search($delete,$gateways);
				array_splice($gateways,$position,1);
				$Settings->save('active_gateways',join(',',$gateways));
			}
		}

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-payments');
			do_action('shopp_save_payment_settings');

			if ( !empty($_POST['gateway']) && isset($Gateways->active[ $_POST['gateway'] ]) ) {
				if ( !in_array($_POST['gateway'],$gateways) ) {
					$gateways[] = $_POST['gateway'];
					$Settings->save('active_gateways',join(',',$gateways));
				}
			}

			$this->settings_save();
			$Gateways->settings();	// Load all installed gateways for settings UIs
			$updated = __('Shopp payments settings saved.','Shopp');
		}

		$installed = array();
		foreach($Gateways->modules as $slug => $module)
			$installed[$slug] = $module->name;

		$Gateways->ui();		// Setup setting UIs
		if ( isset($_REQUEST['id']) && isset($Gateways->active[ $_REQUEST['id'] ]) ) {
			$edit = $_REQUEST['id'];
			$Gateway = $Gateways->get($edit);
			$editor = $Gateway->ui();

		}

		add_action('shopp_gateway_module_settings',array($Gateways,'templates'));
		include(SHOPP_ADMIN_PATH."/settings/payments.php");
	}

	function payments_ui () {
		register_column_headers('shopp_page_shopp-settings-payments', array(
			'cb'=>'<input type="checkbox" />',
			'name'=>__('Name','Shopp'),
			'processor'=>__('Processor','Shopp'),
			'type'=>__('Type','Shopp'),
			'payments'=>__('Payments','Shopp')
		));
	}

	function pages () {

		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-pages');
			$catalog_slug = Storefront::slug();
			$_POST['settings']['storefront_pages'] = Storefront::pages_settings($_POST['settings']['storefront_pages']);
			$this->settings_save();

			// Re-register page, collection, taxonomies and product rewrites
			// so that the new slugs work immediately
			global $Shopp;
			$Shopp->pages();
			$Shopp->collections();
			$Shopp->taxonomies();
			$Shopp->products();

			// If the catalog slug changes
			// $hardflush is false (soft flush... plenty of fiber, no .htaccess update needed)
			$hardflush = ($catalog_slug != Storefront::slug());
			flush_rewrite_rules($hardflush);
		}

		$pages = Storefront::pages_settings();
		include(SHOPP_ADMIN_PATH."/settings/pages.php");

	}

	function pages_ui () {
		register_column_headers('shopp_page_shopp-settings-pages', array(
			'title'=>__('Title','Shopp'),
			'slug'=>__('Slug','Shopp'),
			'decription'=>__('Description','Shopp')
		));
	}

	function images () {

		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$Settings = ShoppSettings();

		$edit = false;
		if (isset($_GET['id'])) $edit = (int)$_GET['id'];

		if (!empty($_GET['delete'])) {
			$delete = (int)$_GET['delete'];
			$Record = new ImageSetting($delete);
			$Record->delete();
			shopp_redirect($this->url);
			exit();
		}

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-images');

			$ImageSetting = new ImageSetting($edit);
			$_POST['name'] = sanitize_title_with_dashes($_POST['name']);
			$_POST['sharpen'] = floatval(str_replace('%','',$_POST['sharpen']));
			$ImageSetting->updates($_POST);
			if (!empty($ImageSetting->name)) $ImageSetting->save();
		}

		$pagenum = absint( $pagenum );
		if ( empty($pagenum) )
			$pagenum = 1;
		if( !$per_page || $per_page < 0 )
			$per_page = 20;
		$start = ($per_page * ($pagenum-1));

		$ImageSetting = new ImageSetting($edit);
		$table = $ImageSetting->_table;
		$where = array(
			"type='$ImageSetting->type'",
			"context='$ImageSetting->context'"
		);
		$limit = "$start,$per_page";

		$options = compact('columns','useindex','table','joins','where','groupby','having','limit','orderby');
		$query = DB::select($options);
		$settings = DB::query($query,'array',array($ImageSetting,'loader'));
		$total = DB::query("SELECT FOUND_ROWS() as total",'auto','col','total');

		$num_pages = ceil($total / $per_page);
		$page_links = paginate_links( array(
			'base' => add_query_arg(array("edit"=>null,'pagenum' => '%#%')),
			'format' => '',
			'total' => $num_pages,
			'current' => $pagenum,
		));

		$fit_menu = $ImageSetting->fit_menu();
		$quality_menu = $ImageSetting->quality_menu();

		$json_settings = array();
		$skip = array('created','modified','numeral','context','type','sortorder','parent');
		foreach ($settings as &$Setting)
			if (method_exists($Setting,'json'))
				$json_settings[$Setting->id] = $Setting->json($skip);

		include(SHOPP_ADMIN_PATH."/settings/images.php");
	}

	function images_ui () {
		register_column_headers('shopp_page_shopp-settings-images', array(
			'name'=>__('Name','Shopp'),
			'dimensions'=>__('Dimensions','Shopp'),
			'fit'=>__('Fit','Shopp'),
			'quality'=>__('Quality','Shopp'),
			'sharpness'=>__('Sharpness','Shopp')
		));
	}

	function system () {
		global $Shopp;
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_system')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		add_action('shopp_storage_module_settings',array(&$this,'storage_ui'));

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-system');

			if (!isset($_POST['settings']['error_notifications']))
				$_POST['settings']['error_notifications'] = array();

			$this->settings_save();

			// Reinitialize Error System
			$Shopp->Errors = new ShoppErrors($this->Settings->get('error_logging'));
			$Shopp->ErrorLog = new ShoppErrorLogging($this->Settings->get('error_logging'));
			$Shopp->ErrorNotify = new ShoppErrorNotification($this->Settings->get('merchant_email'),
										$this->Settings->get('error_notifications'));

			$updated = __('Shopp system settings saved.','Shopp');
		} elseif (!empty($_POST['rebuild'])) {
			$db =& DB::get();

			$assets = DatabaseObject::tablename(ProductImage::$table);
			$query = "DELETE FROM $assets WHERE context='image' AND type='image'";
			if ($db->query($query))
				$updated = __('All cached images have been cleared.','Shopp');
		}


		if (isset($_POST['resetlog'])) $Shopp->ErrorLog->reset();

		$notifications = $this->Settings->get('error_notifications');
		if (empty($notifications)) $notifications = array();

		$notification_errors = array(
			SHOPP_TRXN_ERR => __("Transaction Errors","Shopp"),
			SHOPP_AUTH_ERR => __("Login Errors","Shopp"),
			SHOPP_ADDON_ERR => __("Add-on Errors","Shopp"),
			SHOPP_COMM_ERR => __("Communication Errors","Shopp"),
			SHOPP_STOCK_ERR => __("Inventory Warnings","Shopp")
			);

		$errorlog_levels = array(
			0 => __("Disabled","Shopp"),
			SHOPP_ERR => __("General Shopp Errors","Shopp"),
			SHOPP_TRXN_ERR => __("Transaction Errors","Shopp"),
			SHOPP_AUTH_ERR => __("Login Errors","Shopp"),
			SHOPP_ADDON_ERR => __("Add-on Errors","Shopp"),
			SHOPP_COMM_ERR => __("Communication Errors","Shopp"),
			SHOPP_STOCK_ERR => __("Inventory Warnings","Shopp"),
			SHOPP_ADMIN_ERR => __("Admin Errors","Shopp"),
			SHOPP_DB_ERR => __("Database Errors","Shopp"),
			SHOPP_PHP_ERR => __("PHP Errors","Shopp"),
			SHOPP_ALL_ERR => __("All Errors","Shopp"),
			SHOPP_DEBUG_ERR => __("Debugging Messages","Shopp")
			);

		// Load Storage settings
		$Shopp->Storage->settings();

		// Build the storage options menu
		$storage = array();
		foreach ($Shopp->Storage->active as $module)
			$storage[$module->module] = $module->name;

		$loading = array("shopp" => __('Load on Shopp-pages only','Shopp'),"all" => __('Load on entire site','Shopp'));

		if ($this->Settings->get('error_logging') > 0)
			$recentlog = $Shopp->ErrorLog->tail(500);

		include(SHOPP_ADMIN_PATH."/settings/system.php");
	}

	function storage_ui () {
		global $Shopp;
		$Shopp->Storage->settings();
		$Shopp->Storage->ui();
	}


	function settings_save () {
		if (empty($_POST['settings']) || !is_array($_POST['settings'])) return false;
		foreach ($_POST['settings'] as $setting => $value)
			$this->Settings->save($setting,$value);
	}

} // END class Setup

?>