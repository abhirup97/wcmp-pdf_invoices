<?php

class WCMp_PDF_Invoices {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $shortcode;
    public $admin;
    public $frontend;
    public $template;
    public $ajax;
    private $file;
    public $settings;
    public $utils;
    public $license;
    public $wcmp_wp_fields;

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WCMp_PDF_INVOICES_PLUGIN_TOKEN;
        $this->text_domain = WCMp_PDF_INVOICES_TEXT_DOMAIN;
        $this->version = WCMp_PDF_INVOICES_PLUGIN_VERSION;

        $this->load_plugin_textdomain();

        // DC License Activation
        $this->load_class('license');
        $this->license = new WCMp_PDF_Invoices_License( $this->file, $this->plugin_path, WCMp_PDF_INVOICES_PLUGIN_PRODUCT_ID, $this->version, 'plugin', WCMp_PDF_INVOICES_PLUGIN_SERVER_URL, WCMp_PDF_INVOICES_PLUGIN_SOFTWARE_TITLE, $this->text_domain  );

        add_action('init', array(&$this, 'init'));
        add_filter('woocommerce_email_attachments', array(&$this, 'attach_invoice_to_order_email'), 99, 3);
        add_action('wcmp_init', array(&$this, 'init_after_wcmp_load'));
        
        add_action( 'wcmp_pdf_invoices_before_get_html_render', array(&$this, 'unsupported_currencies_support') );
    }

    function init_after_wcmp_load() {
        add_action('settings_vendor_general_tab_options', array(&$this, 'add_vendor_pdf_invoice_endpoint_option'));
        add_filter('settings_vendor_general_tab_new_input', array(&$this, 'save_vendor_pdf_invoice_endpoint_option'), 10, 2);
        add_filter('wcmp_endpoints_query_vars', array(&$this, 'add_wcmp_endpoints_query_vars'));
        add_filter('wcmp_vendor_dashboard_nav', array(&$this, 'wcmp_pdf_invoice_nav_vendor_dashboard'));
    }

    public function add_vendor_pdf_invoice_endpoint_option($settings_tab_options) {
        $settings_tab_options['sections']['wcmp_vendor_general_settings_endpoint_ssection']['fields']['wcmp_vendor_pdf_invoice_endpoint'] = array('title' => __('PDF Invoice', 'wcmp-pdf_invoices'), 'type' => 'text', 'id' => 'wcmp_vendor_pdf_invoice_endpoint', 'label_for' => 'wcmp_vendor_pdf_invoice_endpoint', 'name' => 'wcmp_vendor_pdf_invoice_endpoint', 'hints' => __('Set endpoint for vendor PDF invoice page', 'wcmp-pdf_invoices'), 'placeholder' => 'pdf-invoice');
        return $settings_tab_options;
    }

    public function save_vendor_pdf_invoice_endpoint_option($new_input, $input) {
        if (isset($input['wcmp_vendor_pdf_invoice_endpoint']) && !empty($input['wcmp_vendor_pdf_invoice_endpoint'])) {
            $new_input['wcmp_vendor_pdf_invoice_endpoint'] = sanitize_text_field($input['wcmp_vendor_pdf_invoice_endpoint']);
        }
        return $new_input;
    }

    public function add_wcmp_endpoints_query_vars($endpoints) {
        $endpoints['pdf-invoice'] = array(
            'label' => __('PDF Invoice', 'wcmp-pdf_invoices'),
            'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_pdf_invoice_endpoint', 'vendor', 'general', 'pdf-invoice')
        );
        if (!get_option('vendor_pdf_invoice_added')) {
            flush_rewrite_rules();
            update_option('vendor_pdf_invoice_added', 1);
        }
        return $endpoints;
    }

    public function wcmp_pdf_invoice_nav_vendor_dashboard($nav) {

        $nav['store-settings']['submenu']['pdf-invoice'] = array(
            'label' => __('Invoice', 'wcmp-pdf_invoices')
            , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_pdf_invoice_endpoint', 'vendor', 'general', 'pdf-invoice'))
            , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_pdf_invoice_capability', true)
            , 'position' => 52
            , 'link_target' => '_self'
        );
        return $nav;
    }

    function attach_invoice_to_order_email($attachments, $status, $order) {
        global $WCMp_PDF_Invoices;
        if (is_object($order) && is_callable(array($order, 'get_id'))) {
            $order_id = $order->get_id();
        } else {
            return $attachments;
        }
        $settings = get_wcmp_pdf_invoices_settings();
        $admin_attach_on_email = isset($settings['attach_to_email_input']) ? $settings['attach_to_email_input'] : '';
        if ($admin_attach_on_email != 'disabled' && $status == $admin_attach_on_email ) {   
            $attachments[] = $WCMp_PDF_Invoices->utils->get_pdf_attachments($order->get_id(), array('pdf_type' => 'invoice', 'user_id' => '', 'user_type' => 'admin'));
        }
        $customer_attach_on_email = isset($settings['customer_attach_to_email']) ? $settings['customer_attach_to_email'] : '';
        if($customer_attach_on_email != 'disabled' && $status == $customer_attach_on_email ){
            $attachments[] = $WCMp_PDF_Invoices->utils->get_pdf_attachments($order->get_id(), array('pdf_type' => 'invoice', 'user_id' => get_current_user_id(), 'user_type' => 'customer'));
        }
        if($customer_attach_on_email != 'disabled' && apply_filters('wcmp_pdf_invoices_vendor_can_send_invoice_to_customer', false) && $status == 'notify_shipped' ){
            $attachments[] = $WCMp_PDF_Invoices->utils->get_pdf_attachments($order->get_id(), array('pdf_type' => 'invoice', 'user_id' => $order->get_user_id(), 'user_type' => 'customer'));
        }
        return $attachments;
    }

    /**
     * initilize plugin on WP init
     */
    function init() {
        global $WCMp, $WCMp_PDF_Invoices;
        
        // init templates
        $this->load_class('template');
        $this->template = new WCMp_PDF_Invoices_Template();

        // init Utils
        $this->load_class('utils');
        $settings = get_wcmp_pdf_invoices_settings();
        $this->utils = new WCMp_PDF_Invoices_Utils($settings);

        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->load_class('ajax');
            $this->ajax = new WCMp_PDF_Invoices_Ajax();
        }

        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new WCMp_PDF_Invoices_Admin();
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new WCMp_PDF_Invoices_Frontend();

            // init shortcode
            $this->load_class('shortcode');
            $this->shortcode = new WCMp_PDF_Invoices_Shortcode();
        }


        // DC Wp Fields
        $this->wcmp_wp_fields = $WCMp->wcmp_wp_fields;
    }
    
    public function unsupported_currencies_support(){
        if( in_array( get_woocommerce_currency(), get_unsupported_wcmp_pdf_invoices_currencies() ) ){
            add_filter( 'woocommerce_currency_symbol', array( $this, 'wrap_currency_symbol' ), 10000, 2 );
            add_filter( 'wcmp_pdf_template_styles', array($this, 'currency_symbol_font_styles' ) );
        }
    }
    
    public function wrap_currency_symbol( $currency_symbol, $currency ) {
        $currency_symbol = sprintf( '<span class="wcmp-pdf-currency-symbol">%s</span>', $currency_symbol );
        return $currency_symbol;
    }

    public function currency_symbol_font_styles ($css) {
        $css .='.wcmp-pdf-currency-symbol { font-family: "Currencies"; }';
        return $css;
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'wcmp-pdf_invoices');
        load_textdomain('wcmp-pdf_invoices', WP_LANG_DIR . '/wcmp-pdf_invoices/wcmp-pdf_invoices-' . $locale . '.mo');
        load_plugin_textdomain('wcmp-pdf_invoices', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
    }

    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

    // End load_class()

    /** Cache Helpers ******************************************************** */

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE'))
            define("DONOTCACHEPAGE", "true");
        // WP Super Cache constant
    }

}
