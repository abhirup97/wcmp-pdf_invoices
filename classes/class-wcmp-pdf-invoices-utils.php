<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WCMp_PDF_Invoices_Utils')) :

    class WCMp_PDF_Invoices_Utils {

        public $settings;

        public function __construct($settings = array()) {
            $this->settings = $settings;
            $this->preview_template();
        }

        /**
         * pdf invoices templates options
         */
        public function get_templates_options() {
            global $WCMp_PDF_Invoices;
            $pdf_templates = apply_filters('wcmp_pdf_invoice_preferred_templates', array(
                'wcmp_pdf_invoice_first_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template1.jpg', 'name' => __('Template 1', 'wcmp-pdf_invoices')),
                'wcmp_pdf_invoice_second_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template2.jpg', 'name' => __('Template 2', 'wcmp-pdf_invoices')),
                'wcmp_pdf_invoice_third_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template3.jpg', 'name' => __('Template 3', 'wcmp-pdf_invoices')),
                'wcmp_pdf_invoice_forth_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template4.jpg', 'name' => __('Template 4', 'wcmp-pdf_invoices')),
                'wcmp_pdf_invoice_fifth_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template5.jpg', 'name' => __('Template 5', 'wcmp-pdf_invoices')),
            ));
            return $pdf_templates;
        }

        /**
         * invoice prefix
         */
        public function get_invoice_prefix() {
            $prefix = '';
            if (isset($this->settings['invoice_no_format']) && !empty($this->settings['invoice_no_format'])) {
                $prefix = str_replace("YEAR", date("Y"), $this->settings['invoice_no_format']);
                $prefix = str_replace("MONTH", date("m"), $prefix);
            }
            return apply_filters('wcmp_pdf_invoices_invoice_prefix', $prefix, $this->settings);
        }

        public function set_date($value) {
            try {
                if (empty($value)) {
                    return;
                }

                if (is_a($value, 'WC_DateTime')) {
                    $datetime = $value;
                } elseif (is_numeric($value)) {
                    // Timestamps are handled as UTC timestamps in all cases.
                    $datetime = new WC_DateTime("@{$value}", new DateTimeZone('UTC'));
                } else {
                    // Strings are defined in local WP timezone. Convert to UTC.
                    if (1 === preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits)) {
                        $offset = !empty($date_bits[7]) ? iso8601_timezone_to_offset($date_bits[7]) : wc_timezone_offset();
                        $timestamp = gmmktime($date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1]) - $offset;
                    } else {
                        $timestamp = wc_string_to_timestamp(get_gmt_from_date(gmdate('Y-m-d H:i:s', wc_string_to_timestamp($value))));
                    }
                    $datetime = new WC_DateTime("@{$timestamp}", new DateTimeZone('UTC'));
                }

                // Set local timezone or offset.
                if (get_option('timezone_string')) {
                    $datetime->setTimezone(new DateTimeZone(wc_timezone_string()));
                } else {
                    $datetime->set_utc_offset(wc_timezone_offset());
                }

                return $datetime;
            } catch (Exception $e) {
                
            }
        }

        /**
         * return file name
         */
        public function get_filename($pdf_type = 'invoice', $order_ids = '', $user_id = '', $args = array() ) {
            global $WCMp_PDF_Invoices;

            $order_count = is_array($order_ids) || is_object($order_ids) ? count($order_ids) : 1;
            if ($pdf_type != 'invoice') {
                $name = _n('packing-slip', 'packing-slips', $order_count, 'wcmp-pdf_invoices');
            } else {
                $name = _n('invoice', 'invoices', $order_count, 'wcmp-pdf_invoices');
            }
            if(isset($args['user_type'])) $user_type = $args['user_type'];
            print_r($user_type);
            
            if($user_type) {
                if($user_type == 'admin'){
                    $name = 'admin-' . $name;
                }elseif($user_type == 'vendor'){
                     $name = 'vendor-' . $name;
                }elseif($user_type == 'customer'){
                    $name = 'customer-'. $name;
                }else{
                    $name = $name;
                }
            } elseif($user_id) {
                $user = get_user_by('ID', $user_id);
                if (in_array('administrator',  $user->roles)){
                    $name = 'admin-' . $name . '-' . $user_id;
                }elseif (in_array('dc_vendor',  $user->roles)) {
                    $name = 'vendor-' . $name . '-' . $user_id;
                }else{
                    $name = $name . '-' . $user_id;
                }
            }

            if ($order_count == 1) {
                $order_ids = (array)$order_count;
                $suffix = $order_ids[0];
            } else {
                $suffix = date('Y-m-d'); // 2020-11-11
            }
            
            $filename = $name . '-' . $suffix . '.pdf';

            // Filter filename
            $filename = apply_filters('wcmp_pdf_invoice_filename', $filename, $pdf_type, $order_ids, $user_id);

            // sanitize filename (after filters to prevent human errors)!
            return sanitize_file_name($filename);
        }

        /**
         * return array of invoice number & date
         */
        public function get_invoice($order_id, $user_id = '') {
            if (!$order_id)
                return false;
            if (!$user_id)
                $user_id = $order_id;
            if (!is_user_wcmp_vendor($user_id))
                $user_id = $order_id;
            $invoice_no_date = get_post_meta($order_id, '_wcmp_pdf_invoice_number_date', true);
            if (!$invoice_no_date)
                return false;
            if (isset($invoice_no_date[$user_id])){
                return $invoice_no_date[$user_id];
            }
        }
        
        /**
         * return footer contents
         */
        public function get_footer_content($pdf_type = '', $args =array()) {
            $footer_content = array(site_url(), get_option('admin_email'));
            return apply_filters('wcmp_pdf_invoice_pdf_footer_content', implode(' | ', $footer_content), $footer_content, $pdf_type, $args);
        }
        
        /**
         * return pdf attachment of an order
         */
        public function get_pdf_attachments($order_id, $args= array()) {
            if (!$order_id)
                return false;
            $default = array('pdf_type' => 'invoice', 'user_id' => '', 'user_type' => '');
            $pdf_args = $args + $default;

            $general_settings = get_wcmp_pdf_invoices_settings();

            $path = $this->get_wcmp_pdf_tmp_path($order_id);
            $file_to_save = $path . '/' .$this->get_filename($pdf_args['pdf_type'], $order_id, $pdf_args['user_id'], $pdf_args['user_type'] );
            $args = array(
                'order_ids' => $order_id, 
                'user_id' => $pdf_args['user_id'], 
                'user_type' => $pdf_args['user_type'],
                'settings' => $general_settings
            );
            $html = $this->get_html('invoice', $args);
            if($pdf_args['pdf_type'] == 'packing_slip'){
                $html = $this->get_html('packing_slip', $args);
            }
            if ($html) {
                $pdf_maker = get_wcmp_pdf_invoices_pdfmaker($html, $general_settings);
                $pdf = $pdf_maker->output();
                if ($pdf) {
                    file_put_contents($file_to_save, $pdf);
                    return $file_to_save;
                }
            }
        }

        /**
         * return the base wcmp_pdf_invoices folder (usually uploads)
         */
        public function init_wcmp_pdf_tmp_base() {
            $upload_dir = wp_upload_dir();
            $upload_base = trailingslashit($upload_dir['basedir']);
            $tmp_base = trailingslashit(apply_filters('wcmp_pdf_invoice_tmp_path', $upload_base . 'wcmp_pdf_invoice/'));
            return $tmp_base;
        }

        /**
         * Return wcmp_pdf_invoices path
         */
        public function get_wcmp_pdf_tmp_path($subs = '') {
            $tmp_base = $this->init_wcmp_pdf_tmp_base();
            // check if tmp folder exists => if not, initialize
            if (!@is_dir($tmp_base)) {
                $this->init_wcmp_pdf_tmp($tmp_base);
            }

            if (empty($subs)) {
                return $tmp_base;
            }

            $tmp_path = $tmp_base . $subs . '/';

            if (!@is_dir($tmp_path)) {
                $this->init_wcmp_pdf_tmp($tmp_path);
            }

            return $tmp_path;
        }

        /**
         * Install/create wcmp_pdf_invoices folders
         */
        public function init_wcmp_pdf_tmp($path) {
            // create plugin base temp folder
            @mkdir($path);
            // create .htaccess file and empty index.php to protect in case an open webfolder is used!
            @file_put_contents($path . '.htaccess', 'deny from all');
            @touch($path . 'index.php');
        }
        
        /**
	 * Copy DOMPDF fonts to wordpress tmp folder
	 */
	public function copy_dompdf_fonts_to_local( $path, $merge_with_local = true ) {
            global $WCMp_PDF_Invoices;
            $path = trailingslashit( $path );
            $dompdf_font_dir = $WCMp_PDF_Invoices->plugin_path . 'lib/dompdf/dompdf/lib/fonts/';

            // get local font dir from filtered options
            $dompdf_options = apply_filters( 'wcmp_pdf_invoices_dompdf_options', array(
                    'defaultFont'		=> 'dejavu sans',
                    'fontDir'			=> $this->get_wcmp_pdf_tmp_path('fonts'),
                    'fontCache'			=> $this->get_wcmp_pdf_tmp_path('fonts'),
                    'isRemoteEnabled'		=> true,
                    'isFontSubsettingEnabled'	=> true,
                    'isHtml5ParserEnabled'	=> true,
            ) );
            $fontDir = $dompdf_options['fontDir'];

            // merge font family cache with local/custom if present
            $font_cache_files = array(
                //'cache'		=> 'dompdf_font_family_cache.php',
                'cache_dist'	=> 'dompdf_font_family_cache.dist.php',
            );
            foreach ( $font_cache_files as $font_cache_name => $font_cache_filename ) {
                $plugin_fonts = @require $dompdf_font_dir . $font_cache_filename;
                if ( $merge_with_local && is_readable( $path . $font_cache_filename ) ) {
                    $local_fonts = @require $path . $font_cache_filename;
                    if (is_array($local_fonts) && is_array($plugin_fonts)) {
                        $local_fonts = array_merge($local_fonts, $plugin_fonts);
                        $fonts_export = var_export($local_fonts,true);
                        $fonts_export = str_replace('\'' . $fontDir , '$fontDir . \'', $fonts_export);
                        $cacheData = sprintf("return %s;%s", $fonts_export, PHP_EOL );
                        file_put_contents($path . $font_cache_filename, $cacheData);
                    } else { 
                        copy( $dompdf_font_dir . $font_cache_filename, $path . $font_cache_filename );
                    }
                } else {
                    copy( $dompdf_font_dir . $font_cache_filename, $path . $font_cache_filename );
                }
            }

            if ( function_exists('glob') ) {
                $files = glob($dompdf_font_dir."*.*");
                foreach($files as $file){
                    $filename = basename($file);
                    if( !is_dir($file) && is_readable($file) && !in_array($filename, $font_cache_files)) {
                        $dest = $path . $filename;
                        copy($file, $dest);
                    }
                }
            } else {
                $extensions = array( '.ttf', '.ufm', '.ufm.php', '.afm', '.afm.php' );
                $fontDir = untrailingslashit($dompdf_font_dir);
                $plugin_fonts = @require $dompdf_font_dir . $font_cache_files['cache'];

                foreach ($plugin_fonts as $font_family => $filenames) {
                    foreach ($filenames as $filename) {
                        foreach ($extensions as $extension) {
                            $file = $filename.$extension;
                            if (file_exists($file)) {
                                $dest = $path . basename($file);
                                copy($file, $dest);
                            }
                        }
                    }
                }
            }
	}

        public function merge_documents($html_content) {
            // insert page breaks merge
            $page_break = "\n<div style=\"page-break-before: always;\"></div>\n";
            $html = implode($page_break, $html_content);
            return apply_filters('wcmp_pdf_merged_bulk_document_content', $html, $html_content, $this);
        }

        public function get_html($pdf_type = 'invoice', $args = array()) {
            global $WCMp_PDF_Invoices;

            $defaults = array(
                'pdf_type' => $pdf_type,
                'order_ids' => '',
                'user_id' => get_current_user_id(),
                'user_type' => 'admin',
                'settings' => $this->settings,
            );
            $pdf_args = $args + $defaults;
            $general_settings = get_wcmp_pdf_invoices_settings();
            if (is_user_wcmp_vendor($pdf_args['user_id'])) {
                $general_settings = get_wcmp_pdf_invoices_settings($pdf_args['user_id']);
            }
            if ($pdf_type == 'invoice') {
                $template = isset($general_settings['choose_invoice_template']) ? $general_settings['choose_invoice_template'] : 'wcmp_pdf_invoice_first_template';
            } else {
                $template = 'wcmp_packing_slip_first_template';
            }
            $pdf_args['template'] = $template;
            $html_content = array();
            do_action( 'wcmp_pdf_invoices_before_get_html_render', $pdf_type, $args );
            $html = '';
            if ($pdf_args['order_ids']) {
                if (is_array($pdf_args['order_ids'])) {
                    foreach ($pdf_args['order_ids'] as $key => $order_id) {
                        $order = wc_get_order($order_id);
                        $pdf_args['order'] = $order;
                        $tpl_html = '';
                        ob_start();
                        $WCMp_PDF_Invoices->template->get_template($template . '.php', $pdf_args);
                        $tpl_html = ob_get_clean();
                        $html_content[$key] = $tpl_html;
                    }
                    $html = $this->wrap_html_content($this->merge_documents($html_content), $pdf_type, $pdf_args);
                } else {
                    $order = wc_get_order(absint($pdf_args['order_ids']));
                    $pdf_args['order'] = $order;
                    $tpl_html = '';
                    ob_start();
                    $WCMp_PDF_Invoices->template->get_template($template . '.php', $pdf_args);
                    $tpl_html = ob_get_clean();
                    $html = $this->wrap_html_content($tpl_html, $pdf_type, $pdf_args);
                    //$html = $tpl_html;
                }
            }
            
            // clean up special characters
            if ( function_exists('utf8_decode') && function_exists('mb_convert_encoding') ) {
                $html = utf8_decode(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            }

            return $html;
        }

        /**
         * PDF html wrapper
         */
        public function wrap_html_content($content, $pdf_type = 'invoice', $args = array()) {
            global $WCMp_PDF_Invoices;
            $template = $this->settings['choose_invoice_template'];
            if (is_user_wcmp_vendor(get_current_user_id())) {
                $template = get_wcmp_pdf_invoices_settings(get_current_user_id(), 'choose_invoice_template');
            }
            $html = '';
            do_action('wcmp_pdf_html_before_wrap_html_content', $content, $pdf_type, $args, $this );
            ob_start();
            $WCMp_PDF_Invoices->template->get_template("wcmp_pdf_html_wrapper.php", array('content' => $content, 'pdf_type' => $pdf_type, 'template' => $template, 'pdf_args' => $args));
            $html = ob_get_clean();
            return $html;
        }

        /**
         * Output template styles
         */
        public function template_styles() {
            global $WCMp_PDF_Invoices;
            $css = apply_filters('wcmp_pdf_template_styles_file', $WCMp_PDF_Invoices->plugin_path . 'assets/pdf/style.css');

            ob_start();
            if (file_exists($css)) {
                include($css);
            }
            $css = ob_get_clean();
            $css = apply_filters('wcmp_pdf_template_styles', $css, $this);

            echo $css;
        }

        /**
         * Show logo html
         */
        public function header_logo($user_id) {
            if (is_user_wcmp_vendor($user_id)) {
                $settings = get_user_meta($user_id, 'wcmp_pdf_invoices_settings', true);
                //print_r($settings);die;
                if (isset($settings['vendor_invoice_logo']))
                    $logo = $settings['vendor_invoice_logo'];
                else
                    $logo = $this->settings['company_logo'];
            } else {
                $logo = $this->settings['company_logo'];
            }
            $company = $this->company_name($user_id);
            
            if ($logo) {
                if (!is_numeric($logo)) {
                    $logo = get_attachment_id_by_url( $logo );
                }
                $attachment_path = get_attached_file( $logo );

                if ( apply_filters('wcmp_pdf_invoice_header_logo_use_path', true) ) {
                    $attachment_path = get_attached_file( $logo );
                    if( file_exists( $attachment_path ) )
                    $src = $attachment_path;
                } else {
                    $src = get_url_from_upload_field_value( $logo, true );
                }
                
                $attachment_width = apply_filters('wcmp_pdf_invoice_header_logo_width', 110);
                $attachment_classes = apply_filters('wcmp_pdf_invoice_header_logo_classes', 'logo');
                if ($src) {
                    printf('<img src="%1$s" alt="%4$s" />', $src, $attachment_width, $attachment_width, esc_attr($company));
                } else {
                    printf('<h2 class="%1$s">%2$s</h2>', 'header_company_name', esc_attr($company));
                }
            } else {
                printf('<h2 class="%1$s">%2$s</h2>', 'header_company_name', esc_attr($company));
            }
        }

        /**
         * Show company name
         */
        public function company_name($user_id, $user_type = 'admin', $order = array()) {
            if (is_user_wcmp_vendor($user_id) && $user_type == 'vendor') {
                $vendor = get_wcmp_vendor($user_id);
                return $vendor->page_title;
            }elseif($user_type == 'customer' && $order){
                return $order->get_billing_first_name(). ' '. $order->get_billing_last_name();
            }else {
                return get_bloginfo('name');
            }
        }
        
        /**
         * Show order itemmeta
         */
        public function show_order_itemmeta() {
            $show_order_itemmeta = apply_filters('wcmp_pdf_invoice_template_show_order_itemmeta', array(
                '_qty',
                '_vendor_id',
                    ));
            return $show_order_itemmeta;
        }

        /**
         * Hidden order itemmeta
         */
        public function hidden_order_itemmeta() {
            $hidden_order_itemmeta = apply_filters('wcmp_pdf_invoice_template_hidden_order_itemmeta', array(
                '_qty',
                '_tax_class',
                '_product_id',
                '_variation_id',
                '_line_subtotal',
                '_line_subtotal_tax',
                '_line_total',
                '_line_tax',
                'method_id',
                'cost',
                '_vendor_id',
                    ));
            return $hidden_order_itemmeta;
        }

        /**
         * Show invoice to address
         */
        public function invoice_to_address($user_type = 'admin', $user_id = '', $sep = '', $order = '', $pdf_type = '') {
            $invoice_to_address = '';
            if($user_type == 'admin'){
                $user = get_user_by( 'email', get_option( 'admin_email' ) );
                if($user){
                    $invoice_to_address = wc_get_account_formatted_address('billing', $user->ID);
                }else{
                    return get_option( 'admin_email' );
                }
            }elseif (is_user_wcmp_vendor($user_id) && $user_type == 'vendor') {
                $vendor = get_wcmp_vendor($user_id);
                $address = apply_filters('wcmp_pdf_invoice_vendor_address_pre_formatted', array(
                    'address_1' => $vendor->address_1,
                    'address_2' => $vendor->address_2,
                    'city' => $vendor->city,
                    'state' => $vendor->state,
                    'postcode' => $vendor->postcode,
                    'country' => $vendor->country,
                        ), $vendor->id);

                $invoice_to_address = WC()->countries->get_formatted_address($address);
            } else {
                if(!empty($order)){
                    $user_id = $order->get_customer_id();
                    if($user_id){
                        if($pdf_type == "packing_slip") $invoice_to_address = wc_get_account_formatted_address('shipping', $user_id);
                        else $invoice_to_address = wc_get_account_formatted_address('billing', $user_id);
                    }else{
                         if($pdf_type == "packing_slip") $invoice_to_address = $order->get_formatted_shipping_address();
                        else $invoice_to_address =  $order->get_formatted_billing_address();
                    }
                }
            }
            $sep = apply_filters('wcmp_pdf_invoice_address_separator', $sep, $user_id, $user_type);
            if($sep){
                $invoice_to_address = explode('<br/>', $invoice_to_address);
                $invoice_to_address = implode( $sep, $invoice_to_address );
            }
            return $invoice_to_address;
        }

        /**
         * Return the order items
         */
        public function get_order_items($order_id, $user_id = 0) {
            if ($order_id) {
                $order = wc_get_order($order_id);
                if (is_user_wcmp_vendor($user_id)) {
                    $vendor = get_wcmp_vendor($user_id);
                    return $vendor->get_vendor_items_from_order($order_id, $vendor->term_id);
                } else {
                    return $order->get_items();
                }
            }
            return false;
        }

        /**
         * Return the order items totals
         */
        public function get_order_item_totals($order_id, $user_id = 0, $user_type = 'admin', $key = '') {
            if ($order_id) {
                $order_item_totals = array();
                $order = wc_get_order($order_id);
                if (is_user_wcmp_vendor($user_id)) {
                    $vendor = get_wcmp_vendor($user_id);
                    $settings = get_wcmp_pdf_invoices_settings($vendor->id);
                    $order_item_totals = $vendor->wcmp_vendor_get_order_item_totals($order, $vendor->term_id);
                } else {
                    $settings = $this->settings;
                    $order_item_totals = $order->get_order_item_totals();
                }
                $wcmp_pdf_setting_array = array('cart_subtotal' => 'is_subtotal', 'discount' => 'is_discount', 'shipping' => 'is_shipping', 'tax' => 'is_tax');
                $wcmp_pdf_vendor_setting_array = array('commission_subtotal' => 'is_subtotal', 'tax_subtotal' => 'is_tax', 'shipping_subtotal' => 'is_shipping');
                if ($order_item_totals) {
                    $item_totals = array();
                    foreach ($order_item_totals as $item_key => $item_value) {
                        if ($item_key == 'payment_method')
                            continue;
                        if(is_user_wcmp_vendor($user_id)){
                            if (array_key_exists($item_key, $wcmp_pdf_setting_array)) {
                                if (isset($settings[$wcmp_pdf_setting_array[$item_key] . '_vendor']) && $settings[$wcmp_pdf_setting_array[$item_key] . '_vendor'] == 'Enable') {
                                    $item_totals[$item_key] = $item_value;
                                }
                            } elseif(array_key_exists($item_key, array('commission_subtotal' => 'is_subtotal', 'tax_subtotal' => 'is_tax', 'shipping_subtotal' => 'is_shipping'))) {
                                if (isset($settings[$wcmp_pdf_vendor_setting_array[$item_key] . '_vendor']) && $settings[$wcmp_pdf_vendor_setting_array[$item_key] . '_vendor'] == 'Enable') {
                                    $item_totals[$item_key] = $item_value;
                                }
                            }else{
                                $item_totals[$item_key] = $item_value;
                            }
                        }else{
                            if (array_key_exists($item_key, $wcmp_pdf_setting_array)) {
                                if (isset($settings[$wcmp_pdf_setting_array[$item_key] . '_' . $user_type]) && $settings[$wcmp_pdf_setting_array[$item_key] . '_' . $user_type] == 'Enable') {
                                    $item_totals[$item_key] = $item_value;
                                }
                            } elseif(array_key_exists($item_key, array('commission_subtotal' => 'is_subtotal', 'tax_subtotal' => 'is_tax', 'shipping_subtotal' => 'is_shipping'))) {
                                if (isset($settings[$wcmp_pdf_vendor_setting_array[$item_key] . '_' . $user_type]) && $settings[$wcmp_pdf_vendor_setting_array[$item_key] . '_' . $user_type] == 'Enable') {
                                    $item_totals[$item_key] = $item_value;
                                }
                            }else{
                                $item_totals[$item_key] = $item_value;
                            }
                        }
                    }
                    
                    if(is_user_wcmp_vendor($user_id) && $user_type == 'customer'){
                        if(isset($item_totals['commission_subtotal'])) unset($item_totals['commission_subtotal']);
                        $items_total = 0;
                        foreach ($this->get_order_items($order_id, $user_id) as $item_id => $item ) {
                            $item_obj = $order->get_item($item_id);
                            $items_total += $item_obj->get_total();
                        }
                        $vendor_totals = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order));
                        $item_totals['total']['value'] = wc_price($items_total + $vendor_totals['shipping_amount'] + $vendor_totals['tax_amount'] + $vendor_totals['shipping_tax_amount'], array( 'currency' => $order->get_currency() ));
                    }
                    
                    if($key){
                        return isset($item_totals[$key]) ? $item_totals[$key] : array('label'=>'','value' => '');
                    }
                    return $item_totals;
                }
            }
            return false;
        }

        
        /**
         * Return the order details table as html
         */
        public function get_order_details_table_as_html($hook_data = array()) {
        	if(count($hook_data) == 0) return;
			?>
			<div class="product-detail-wrap <?php echo $hook_data["template"]; ?>">
				<table class="product-price-table">
					<thead>
						<tr>
							<?php do_action('before_wcmp_pdf_invoice_template_items_table_header', $hook_data); ?>
							<th width="50%"><?php _e('Item', 'wcmp-pdf_invoices'); ?></th>
							<th align="center"><?php _e('Price', 'wcmp-pdf_invoices'); ?></th>
							<th align="center"><?php _e('Quantity', 'wcmp-pdf_invoices'); ?></th>
							<th align="right"><?php _e('Total', 'wcmp-pdf_invoices'); ?></th>
							<?php do_action('after_wcmp_pdf_invoice_template_items_table_header', $hook_data); ?>
						</tr>
					</thead>
					<tbody>
						<?php $items = $this->get_order_items($hook_data["order"]->get_id(),$hook_data["user_id"]); 
						if( sizeof( $items ) > 0 ) : 
						foreach( $items as $item_id => $item ) : $item_obj = $hook_data["order"]->get_item($item_id); ?>
							<tr>
								<?php do_action('before_wcmp_pdf_invoice_template_items_table_data', $hook_data); ?>
								<td class="product">
									<span class="item-name"><?php echo esc_html( $item_obj->get_name() ); ?></span>
									<?php do_action( 'before_wcmp_pdf_invoice_template_item_meta', $hook_data["pdf_type"], $item, $hook_data["order"] ); ?>
									<ul class="meta">
									<?php $show_order_itemmeta = $this->show_order_itemmeta();
									if ( $meta_data = $item_obj->get_formatted_meta_data( '' ) ) :
										foreach ( $meta_data as $meta_id => $meta ) :
											if ( !in_array( $meta->key, $show_order_itemmeta, true ) ) {
												continue;
											}
											if($meta->key == '_vendor_id'){
												$meta->display_key = __('Sold by', 'wcmp-pdf_invoices');
												$vendor = get_wcmp_vendor($meta->value);
												$meta->display_value = $vendor->page_title;
											}
									?>
									<li class="item-meta"><span><?php echo wp_kses_post( $meta->display_key ); ?>:</span> <span><?php echo wp_kses_post( force_balance_tags( $meta->display_value ) ); ?></span></li>
									<?php endforeach; endif; ?>
									</ul>
									<?php do_action( 'after_wcmp_pdf_invoice_template_item_meta', $hook_data["pdf_type"], $item, $hook_data["order"] ); ?>
								</td>
								<td class="price" align="center"><?php echo wc_price( $hook_data["order"]->get_item_total( $item_obj, false, true ), array( 'currency' => $hook_data["order"]->get_currency() ) ); ?></td>
								<td class="qunatity" align="center"><?php echo "&times; " . esc_html( $item_obj->get_quantity() ); ?></td>
								<td class="total" align="right"><?php echo wc_price( $item_obj->get_total(), array( 'currency' => $hook_data["order"]->get_currency() ) ); ?></td>
								<?php do_action('after_wcmp_pdf_invoice_template_items_table_data', $hook_data); ?>
							</tr>
						<?php endforeach; endif; ?>
					</tbody>
					<tfoot align="right">
						<?php $order_item_totals = $this->get_order_item_totals($hook_data["order"]->get_id(), $hook_data["user_id"], $hook_data["user_type"]);
						if($order_item_totals) : 
							foreach ($order_item_totals as $key => $total) { ?>
								<tr>
									<td colspan="2"></td>
									<td><?php echo $total['label'];?></td>
									<td><?php echo $total['value'];?></td>
								</tr>
							<?php }
						endif;
						?>
					</tfoot>
				</table>
			</div>
		<?php
        }
        
        
        /**
         * Return the order items totals
         */
        public function get_terms_n_conditions($user_type = 'admin', $user_id = 0) {
            if (is_user_wcmp_vendor($user_id) && $user_type == 'vendor') {
                $vendor = get_wcmp_vendor($user_id);
                $settings = get_wcmp_pdf_invoices_settings($vendor->id);
            } else {
                $settings = $this->settings;
            }
            $tnc_key = 'term_and_conditions_'. $user_type;
            if(isset($settings[$tnc_key])){ 
                return $settings[$tnc_key];
            }else{
                return false;
            }
        }
        
        /**
         * Remove attachments older than 1 week (daily, hooked into wp_scheduled_delete )
         */
        public function attachments_cleanup() {
            if (!function_exists("glob") || !function_exists('filemtime')) {
                // glob is disabled
                return;
            }

            $delete_timestamp = apply_filters('wcmp_pdf_invoices_attachments_cleanup_timestamp', time() - ( MONTH_IN_SECONDS ));

            $tmp_path = $this->get_wcmp_pdf_tmp_path();

            if ($folders = glob($tmp_path . '*')) { // get all pdf files
                foreach ($folders as $folder) {
                    $this->recursiveRemove($folder, $delete_timestamp);
                }
            }
        }

        /**
         * Remove folders and its files
         */
        public function recursiveRemove($path, $delete_timestamp = '') {
            $structure = glob($path . '/*');
            if (is_array($structure) && count($structure) > 0) {
                foreach ($structure as $file) {
                    if (is_dir($file)) {
                        $this->recursiveRemove($file);
                    } elseif (is_file($file) && $delete_timestamp) {
                        $file_timestamp = filemtime($file);
                        if (!empty($file_timestamp) && $file_timestamp < $delete_timestamp) {
                            @unlink($file);
                        }
                    } else {
                        @unlink($file);
                    }
                }
            } else {
                @rmdir($path);
            }
        }

        /**
         * Preview pdf template.
         */
        public function preview_template() {
            global $WCMp_PDF_Invoices;
            if ( isset( $_GET['preview_pdf_template'] ) ) {
                if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'preview-wcmp-pdf-tpl' ) ) {
                        die( 'Security check' );
                }
                $template = isset($_REQUEST['template']) ? sanitize_text_field($_REQUEST['template']) : 'wcmp_pdf_invoice_first_template';
                $tpl_html = '';
                ob_start();
                $WCMp_PDF_Invoices->template->get_template("wcmp_pdf_preview_template_html.php", array('template' => $template));
                $tpl_html = ob_get_clean(); ?>
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                        <title><?php _e( 'Preview PDF Template', 'wcmp-pdf_invoices') ?></title>
                        <style type="text/css"><?php $this->template_styles(); ?></style>
                    </head>
                    <body class="wcmp-pdf-preview-body <?php echo $template; ?>">
                        <?php echo $tpl_html; ?>
                    </body>
                </html>
                <?php exit;
            }
        }

    }

endif; // class_exists