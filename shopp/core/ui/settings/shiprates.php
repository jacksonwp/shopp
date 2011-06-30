<div class="wrap shopp">
	<?php if (!empty($updated)): ?><div id="message" class="updated fade"><p><?php echo $updated; ?></p></div><?php endif; ?>

	<div class="icon32"></div>
	<h2><?php _e('Shipping Rates','Shopp'); ?></h2>

	<?php $this->shipping_menu(); ?>

	<form action="<?php echo esc_url($this->url); ?>" id="shipping" method="post">
	<div>
		<?php wp_nonce_field('shopp-settings-shiprate'); ?>
	</div>

	<br class="clear" />

	<div class="tablenav">
		<div class="actions">
			<select name="id" id="shipping-option-menu">
			<option value=""><?php _e('Add a shipping method&hellip;','Shopp'); ?></option>
			<?php echo menuoptions($installed,false,true); ?>
			</select>
			<button type="submit" name="add-shipping-option" id="add-shipping-option" class="button-secondary hide-if-js" tabindex="9999"><?php _e('Add Shipping Option','Shopp'); ?></button>
		</div>
	</div>

	<table class="widefat" cellspacing="0">
		<thead>
		<tr><?php print_column_headers('shopp_page_shopp-settings-shipping'); ?></tr>
		</thead>
		<tfoot>
		<tr><?php print_column_headers('shopp_page_shopp-settings-shipping',false); ?></tr>
		</tfoot>
		<tbody id="shiprates" class="list">
		<?php

			if ($edit && !isset($shiprates[$edit])) {
				$template_data = array(
					'${cancel_href}' => $this->url
				);
				$editor = str_replace(array_keys($template_data),$template_data,$editor);
				$editor = preg_replace('/\${\w+}/','',$editor);

				echo $editor;
			}

			if (count($shiprates) == 0 && !$edit): ?>
				<tr id="no-shiprate-settings"><td colspan="6"><?php _e('No shipping methods, yet.','Shopp'); ?></td></tr>
			<?php
			endif;

			$hidden = get_hidden_columns('shopp_page_shopp-settings-shiprates');
			$even = false;
			foreach ($shiprates as $setting => $module):
				$shipping = shopp_setting($setting);
				$service = $Shipping->modules[$module]->name;

				$label = $service;
				if (isset($shipping['label'])) $label = $shipping['label'];

				$editurl = wp_nonce_url(add_query_arg(array('id'=>$setting),$this->url));
				$deleteurl = wp_nonce_url(add_query_arg(array('delete'=>$setting),$this->url),'shopp_delete_shiprate');

				$classes = array();
				if (!$even) $classes[] = 'alternate'; $even = !$even;

				if ($edit && $edit == $setting) {
					$template_data = array(
						'${cancel_href}' => $this->url
					);
					$editor = str_replace(array_keys($template_data),$template_data,$editor);
					$editor = preg_replace('/\${\w+}/','',$editor);

					echo $editor;
					if ($edit == $setting) continue;
				}

			?>
		<tr class="<?php echo join(' ',$classes); ?>" id="shipping-setting-<?php echo sanitize_title_with_dashes($module); ?>">
			<td class="name column-name"><a href="<?php echo esc_url($editurl); ?>" title="<?php _e('Edit','Shopp'); ?> &quot;<?php echo esc_attr($label); ?>&quot;" class="edit row-title"><?php echo esc_html($label); ?></a>
				<div class="row-actions">
					<span class='edit'><a href="<?php echo esc_url($editurl); ?>" title="<?php _e('Edit','Shopp'); ?> &quot;<?php echo esc_attr($label); ?>&quot;" class="edit"><?php _e('Edit','Shopp'); ?></a> | </span><span class='delete'><a href="<?php echo esc_url($deleteurl); ?>" title="<?php _e('Delete','Shopp'); ?> &quot;<?php echo esc_attr($label); ?>&quot;" class="delete"><?php _e('Delete','Shopp'); ?></a></span>
				</div>
			</td>
			<td class="type column-type"><?php echo esc_html($service); ?></td>
			<td class="supported column-supported"></td>

		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	</form>

</div>

<?php do_action('shopp_shipping_module_settings'); ?>

<script type="text/javascript">
/* <![CDATA[ */
var shipping = <?php echo json_encode(array_map('sanitize_title_with_dashes',array_keys($installed))); ?>,
	defaults = <?php echo json_encode($defaults); ?>,
	settings = <?php echo json_encode($settings); ?>,
	lookup = <?php echo json_encode($lookup); ?>;
/* ]]> */
</script>