<?php
if (!function_exists('woocommerce_inactive_notice')) {

    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sWCMp Vendor PDF Invoices is inactive.%s The %sWooCommerce%s plugin must be active for the WCMp Vendor PDF Invoices to work. Please %sinstall & activate WooCommerce%s', 'wcmp-pdf_invoices'), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugin-install.php?tab=search&s=woocommerce') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('wcmp_inactive_notice')) {

    function wcmp_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sWCMp Vendor PDF Invoices is inactive.%s The %sWC Marketplace%s plugin must be active for the WCMp Vendor PDF Invoices to work. Please %sinstall & activate WC Marketplace%s', 'wcmp-pdf_invoices'), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/dc-woocommerce-multi-vendor/">', '</a>', '<a href="' . admin_url('plugin-install.php?tab=search&s=wc+marketplace') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('wcmp_get_vendor_items_from_order')) {

    function wcmp_get_vendor_items_from_order($order_id) {
        $vendors = array();
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('line_item');
            if ($items) {
                foreach ($items as $item_id => $item) {
                    $product_id = wc_get_order_item_meta($item_id, '_product_id', true);

                    if ($product_id) {
                        $product_vendors = get_wcmp_product_vendors($product_id);
                        if (!empty($product_vendors)) {
                            $vendors[$product_vendors->term_id] = get_wcmp_vendor_by_term($product_vendors->term_id);
                        }
                    }
                }
            }
        }
        return $vendors;
    }

}

if (!function_exists('get_wcmp_pdf_invoices_settings')) {

    function get_wcmp_pdf_invoices_settings($vendor_id = '', $key = '') {
        $settings = get_option('wcmp_pdf_invoices_settings_name');
        if(is_user_wcmp_vendor($vendor_id)){
            $v_settings = get_user_meta($vendor_id, 'wcmp_pdf_invoices_settings', true);
            $settings = wp_parse_args($v_settings, $settings);
        }
        if($key && is_array($settings)){
            if(isset($settings[$key])){
                return $settings[$key];
            }else{
                return false;
            }
        }else{
            return $settings;
        }
    }
}

if (!function_exists('get_wcmp_pdf_invoices_pdfmaker')) {

    function get_wcmp_pdf_invoices_pdfmaker($html, $settings = array()) {
        global $WCMp_PDF_Invoices;
        if (!class_exists('WCMp_PDF_Invoices_PDF_Maker')) {
            include_once( $WCMp_PDF_Invoices->plugin_path . '/classes/class-wcmp-pdf-invoices-pdfmaker.php' );
        }
        $class = apply_filters('wcmp_pdf_invoice_pdfmaker', 'WCMp_PDF_Invoices_PDF_Maker');
        return new $class($html, $settings);
    }

}

if (!function_exists('wcmp_pdf_invoices_pdf_headers')) {

    function wcmp_pdf_invoices_pdf_headers($filename, $mode = 'inline', $pdf = null) {
        switch ($mode) {
            case 'download':
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Transfer-Encoding: binary');
                header('Connection: Keep-Alive');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                break;
            case 'inline':
            default:
                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="' . $filename . '"');
                break;
        }
    }

}


if(!function_exists('get_unsupported_wcmp_pdf_invoices_currencies')){
    function get_unsupported_wcmp_pdf_invoices_currencies(){
        // Unsupported currency symbols 
        return apply_filters( 'get_unsupported_wcmp_pdf_invoices_currencies', array (
                'AED',
                'AFN',
                'BDT',
                'BHD',
                'BTC',
                'CRC',
                'DZD',
                'GEL',
                'GHS',
                'ILS',
                'INR',
                'IQD',
                'IRR',
                'IRT',
                'JOD',
                'KHR',
                'KPW',
                'KRW',
                'KWD',
                'LAK',
                'LBP',
                'LKR',
                'LYD',
                'MAD',
                'MNT',
                'MUR',
                'MVR',
                'NPR',
                'OMR',
                'PHP',
                'PKR',
                'PYG',
                'QAR',
                'RUB',
                'SAR',
                'SCR',
                'SDG',
                'SYP',
                'THB',
                'TND',
                'TRY',
                'UAH',
                'YER',
        ) );
    }
}

