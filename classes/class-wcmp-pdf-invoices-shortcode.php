<?php
class WCMp_PDF_Invoices_Shortcode {

	public $list_product;

	public function __construct() {
		//shortcodes
		add_shortcode('vendor_invoice_settings', array(&$this, 'vendor_invoice_settings'));
	}

	public function vendor_invoice_settings($attr) {
		global $WCMp_PDF_Invoices;
		$this->load_class('vendor-edit-invoice');
		return $this->shortcode_wrapper(array('WCMp_Vendor_Invoice_Edit_Settings', 'output'));
	}

	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	public function load_class($class_name = '') {
		global $WCMp_PDF_Invoices;
		if ('' != $class_name && '' != $WCMp_PDF_Invoices->token) {
			require_once ('shortcode/class-' . esc_attr($WCMp_PDF_Invoices->token) . '-shortcode-' . esc_attr($class_name) . '.php');
		}
	}

}
?>
