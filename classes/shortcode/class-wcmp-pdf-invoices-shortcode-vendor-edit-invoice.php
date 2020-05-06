<?php
class WCMp_Vendor_Invoice_Edit_Settings {

	public function __construct() {

	}

	/**
	 * Output the demo shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $attr ) {
		global $WCMp, $WCMp_PDF_Invoices;
		$WCMp_PDF_Invoices->nocache();
		
		$frontend_script_path = $WCMp_PDF_Invoices->plugin_url . 'assets/frontend/js/';
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		$WCMp->library->load_upload_lib();
		
		wp_enqueue_script('vendor_pdf_settings', $frontend_script_path.'vendor_pdf_settings.js', array('jquery'), $WCMp_PDF_Invoices->version, true);
		
		$current_user_id = get_current_user_id();
		$vendor_obj = get_wcmp_vendor($current_user_id);
		if(function_exists('is_user_wcmp_vendor') && is_user_wcmp_vendor($current_user_id)) {		
			
			$general_settings = get_option('wcmp_pdf_invoices_settings_name');
			
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$save_settings = array();
				$save_settings['choose_preferred_template'] = isset($_POST['choose_preferred_template']) ? $_POST['choose_preferred_template'] : '';
				$save_settings['vendor_invoice_logo'] = isset($_POST['vendor_invoice_logo']) ? $_POST['vendor_invoice_logo'] : '';
				
				if(isset($_POST['is_sku_vendor']) && !empty($_POST['is_sku_vendor'])) $save_settings['is_sku_vendor'] = $_POST['is_sku_vendor'];
				if(isset($_POST['is_subtotal_vendor']) && !empty($_POST['is_subtotal_vendor'])) $save_settings['is_subtotal_vendor'] = $_POST['is_subtotal_vendor'];
				if(isset($_POST['is_discount_vendor']) && !empty($_POST['is_discount_vendor'])) $save_settings['is_discount_vendor'] = $_POST['is_discount_vendor'];
				if(isset($_POST['is_tax_vendor']) && !empty($_POST['is_tax_vendor'])) $save_settings['is_tax_vendor'] = $_POST['is_tax_vendor'];
				if(isset($_POST['is_shipping_vendor']) && !empty($_POST['is_shipping_vendor'])) $save_settings['is_shipping_vendor'] = $_POST['is_shipping_vendor'];
				if(isset($_POST['is_payment_method_vendor']) && !empty($_POST['is_payment_method_vendor'])) $save_settings['is_payment_method_vendor'] =  $_POST['is_payment_method_vendor'];
				if(isset($_POST['is_customer_note_vendor']) && !empty($_POST['is_customer_note_vendor'])) $save_settings['is_customer_note_vendor'] =  $_POST['is_customer_note_vendor'];
				$save_settings['intro_text_vendor'] = isset($_POST['intro_text_vendor']) ? $_POST['intro_text_vendor'] : '';
				$save_settings['term_and_conditions_vendor'] = isset($_POST['term_and_conditions_vendor']) ? $_POST['term_and_conditions_vendor'] : '';
				$save_settings['spcl_notes_from_vendor'] = isset($_POST['spcl_notes_from_vendor']) ? $_POST['spcl_notes_from_vendor'] : '';
				
                                $save_settings = apply_filters('before_wcmp_pdf_invoices_save_settings', $save_settings, $current_user_id);
				update_user_meta($current_user_id, 'wcmp_pdf_invoices_settings', $save_settings);
			}
			
			$get_user_settings = get_user_meta($current_user_id, 'wcmp_pdf_invoices_settings', true);
			$settings = array();
			
			if(!$get_user_settings) {
				$settings['choose_preferred_template'] = isset($general_settings['choose_invoice_template']) ? $general_settings['choose_invoice_template'] : '';
				$settings['vendor_invoice_logo'] = isset($general_settings['company_logo']) ? $general_settings['company_logo'] : '';
				$settings['is_sku_vendor'] = isset($general_settings['is_sku_vendor']) ? $general_settings['is_sku_vendor'] : '';
				$settings['is_subtotal_vendor'] = isset($general_settings['is_subtotal_vendor']) ? $general_settings['is_subtotal_vendor'] : '';
				$settings['is_discount_vendor'] = isset($general_settings['is_discount_vendor']) ? $general_settings['is_discount_vendor'] : '';
				$settings['is_tax_vendor'] = isset($general_settings['is_tax_vendor'])? $general_settings['is_tax_vendor'] : '';
				$settings['is_shipping_vendor'] = isset($general_settings['is_shipping_vendor']) ? $general_settings['is_shipping_vendor'] : '';
				$settings['is_payment_method_vendor'] = isset($general_settings['is_payment_method_vendor']) ?  $general_settings['is_payment_method_vendor'] : '';
				$settings['intro_text_vendor'] = isset($general_settings['intro_text_vendor']) ? $general_settings['intro_text_vendor'] : '';
				$settings['term_and_conditions_vendor'] = isset($general_settings['term_and_conditions_vendor']) ? $general_settings['term_and_conditions_vendor'] : '';
				$settings['is_customer_note_vendor'] = isset($general_settings['is_customer_note_vendor']) ? $general_settings['is_customer_note_vendor'] : '';
			} else {
				$settings['choose_preferred_template'] = isset($get_user_settings['choose_preferred_template']) ? $get_user_settings['choose_preferred_template'] : '';
				$settings['vendor_invoice_logo'] = isset($get_user_settings['vendor_invoice_logo']) ? $get_user_settings['vendor_invoice_logo'] :  '';
				$settings['is_sku_vendor'] = isset($get_user_settings['is_sku_vendor']) ? $get_user_settings['is_sku_vendor'] :  '';
				$settings['is_subtotal_vendor'] = isset($get_user_settings['is_subtotal_vendor']) ? $get_user_settings['is_subtotal_vendor'] :   '';
				$settings['is_discount_vendor'] = isset($get_user_settings['is_discount_vendor']) ? $get_user_settings['is_discount_vendor'] :  '';
				$settings['is_tax_vendor'] = isset($get_user_settings['is_tax_vendor'])? $get_user_settings['is_tax_vendor'] : '';
				$settings['is_shipping_vendor'] = isset($get_user_settings['is_shipping_vendor']) ? $get_user_settings['is_shipping_vendor'] : '';
				$settings['is_payment_method_vendor'] = isset($get_user_settings['is_payment_method_vendor']) ?  $get_user_settings['is_payment_method_vendor'] : '';
				$settings['intro_text_vendor'] = isset($get_user_settings['intro_text_vendor']) ? $get_user_settings['intro_text_vendor'] : '';
				$settings['term_and_conditions_vendor'] = isset($get_user_settings['term_and_conditions_vendor']) ? $get_user_settings['term_and_conditions_vendor'] : '';
				$settings['is_customer_note_vendor'] = isset($get_user_settings['is_customer_note_vendor']) ? $get_user_settings['is_customer_note_vendor'] : '';
				$settings['spcl_notes_from_vendor'] = isset($get_user_settings['spcl_notes_from_vendor']) ? $get_user_settings['spcl_notes_from_vendor'] : '';
			}			
			
			$WCMp_PDF_Invoices->template->get_template( 'wcmp_vendor_invoice_edit_settings.php', array('settings' => $settings, 'vendor' => $vendor_obj) );
		}
	}
}
