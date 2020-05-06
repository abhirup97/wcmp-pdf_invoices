<?php
class WCMp_PDF_Invoices_Frontend {

    public function __construct() {
         global $WCMp;
        $wcmp_get_pdf_settings = get_wcmp_pdf_invoices_settings();
                
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));

        add_filter('wcmp_my_account_my_orders_actions', array($this, 'wcmp_my_account_my_orders_actions'), 10, 2);

        add_action('wcmp_vendor_dashboard_pdf-invoice_endpoint', array($this, 'wcmp_vendor_dashboard_pdf_invoice_endpoint'));
        add_action('before_wcmp_vendor_dashboard', array($this, 'save_vendor_pdf_invoice_data')); 
        add_filter('woocommerce_my_account_my_orders_actions', array($this, 'woocommerce_my_account_my_orders_actions'), 30, 2);
        add_action( 'woocommerce_checkout_update_order_meta', array(&$this, 'wcmp_save_invoice_number_date'), 10, 2);
        if(isset($wcmp_get_pdf_settings['status_to_download_suborder_invoice']) && $wcmp_get_pdf_settings['status_to_download_suborder_invoice'] != 'disabled'){
        add_filter('woocommerce_my_account_my_orders_columns', array($this, 'woocommerce_my_account_my_orders_pdf_columns'), 99);

        add_action('woocommerce_my_account_my_orders_column_wcmp_suborder_pdf', array($this, 'woocommerce_my_account_my_orders_column_wcmp_suborder_PDF'), 99);
        }
    }
    
    /**
     * Save pdf invoice number and date
     */
    public function wcmp_save_invoice_number_date($order_id, $data) {
        global $WCMp_PDF_Invoices;
        $general_settings = get_wcmp_pdf_invoices_settings();
        if(isset($general_settings['is_invoice_no'])){
            $order = wc_get_order($order_id);
            $line_items = $order->get_items();
            $time = current_time( 'timestamp', true );
            $invoice_number_date = array();
            $have_vendor_in_array = array();
            if($order){
                $invoice_number_date[$order_id] = array(
                    'invoice_no' => $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $order_id . $time,
                    'invoice_date' => $WCMp_PDF_Invoices->utils->set_date($time)
                );
            }
            
            foreach ( $line_items as $item_id => $item ) {
                $post = get_post($order_id);
                $product_id = $item->get_product_id();
                $time = current_time( 'timestamp', true );
                $have_vendor = get_wcmp_product_vendors($product_id);
                if($have_vendor){
                    if(in_array($have_vendor->id, $have_vendor_in_array))
                        continue;
                    $invoice_number_date[$have_vendor->id] = array(
                        'invoice_no' => $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $have_vendor->id . $time,
                        'invoice_date' => $WCMp_PDF_Invoices->utils->set_date($time)
                    );
                    $have_vendor_in_array[] = $have_vendor->id;
                }elseif($order){
                    $invoice_number_date[$order_id] = array(
                        'invoice_no' => $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $order_id . $time,
                        'invoice_date' => $WCMp_PDF_Invoices->utils->set_date($time)
                    );
                }
            }
            update_post_meta($order_id, '_wcmp_pdf_invoice_number_date', $invoice_number_date);
        }
    }
    
    public function wcmp_vendor_dashboard_pdf_invoice_endpoint() {
        global $WCMp, $WCMp_PDF_Invoices;
        $current_user_id = get_current_user_id();
        $frontend_script_path = $WCMp_PDF_Invoices->plugin_url . 'assets/frontend/js/';
        $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        if(is_user_wcmp_vendor($current_user_id)){
            $WCMp->library->load_frontend_upload_lib();
            wp_enqueue_script('vendor_pdf_settings', $frontend_script_path.'vendor_pdf_settings.js', array('jquery'), $WCMp_PDF_Invoices->version, true);
            $WCMp_PDF_Invoices->template->get_template('vendor-pdf-invoices-settings.php', array());
        }
    }
    
    public function save_vendor_pdf_invoice_data() {
        global $WCMp;
        $vendor = get_current_vendor();
        $wpnonce = isset($_POST['pdf_invoices_nonce']) ? $_POST['pdf_invoices_nonce'] : '';
        $save_settings = array();
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $wpnonce && wp_verify_nonce($wpnonce, 'wcmp_vendor_pdf_invoices_settings')) {
            
            $save_settings['choose_invoice_template'] = isset($_POST['choose_invoice_template']) ? $_POST['choose_invoice_template'] : '';
            $save_settings['vendor_invoice_logo'] = isset($_POST['vendor_invoice_logo']) ? $_POST['vendor_invoice_logo'] : '';
            //$save_settings['is_sku_vendor'] = isset($_POST['is_sku_vendor']) ? $_POST['is_sku_vendor'] : '';
            $save_settings['is_subtotal_vendor'] = isset($_POST['is_subtotal_vendor']) ? $_POST['is_subtotal_vendor'] : '';
            $save_settings['is_discount_vendor'] = isset($_POST['is_discount_vendor']) ? $_POST['is_discount_vendor'] : '';
            $save_settings['is_tax_vendor'] = isset($_POST['is_tax_vendor']) ? $_POST['is_tax_vendor'] : '';
            $save_settings['is_shipping_vendor'] = isset($_POST['is_shipping_vendor']) ? $_POST['is_shipping_vendor'] : '';
            $save_settings['is_payment_method_vendor'] = isset($_POST['is_payment_method_vendor']) ? $_POST['is_payment_method_vendor'] : '';
            $save_settings['is_customer_note_vendor'] = isset($_POST['is_customer_note_vendor']) ? $_POST['is_customer_note_vendor'] : '';
            $save_settings['intro_text_vendor'] = isset($_POST['intro_text_vendor']) ? $_POST['intro_text_vendor'] : '';
            $save_settings['term_and_conditions_vendor'] = isset($_POST['term_and_conditions_vendor']) ? $_POST['term_and_conditions_vendor'] : '';

            $save_settings = apply_filters('before_wcmp_pdf_invoices_save_settings', $save_settings, $_POST, $vendor);
            
            if(update_user_meta($vendor->id, 'wcmp_pdf_invoices_settings', $save_settings)){
                wc_add_notice(__('PDF invoices settings updated!', 'wcmp-pdf_invoices'), 'success');
            }else{
                wc_add_notice(__('Somethings are wrong!', 'wcmp-pdf_invoices'), 'error');
            }
        }
    }

    function create_pdf_invoice_per_order($order_id, $posted_data, $order) {
        global $WCMp_PDF_Invoices;
        $order = wc_get_order($order_id);
        $general_settings = get_wcmp_pdf_invoices_settings();
        $template = isset($general_settings['choose_invoice_template']) ? $general_settings['choose_invoice_template'] : 'wcmp_pdf_invoice_first_template';
        $path = $WCMp_PDF_Invoices->utils->get_wcmp_pdf_tmp_path($order_id);
        $file_to_save = $path . '/' .$WCMp_PDF_Invoices->utils->get_filename('invoice', $order_id, '' );

//        if (!file_exists($base_path2)) {
//            mkdir($base_path2, 0777, true);
//        }
        $args = array(
            'order_ids' => $order_id, 
            'user_id' => '', 
            'user_type' => '',
            'settings' => $general_settings
        );
        $html = $WCMp_PDF_Invoices->utils->get_html('invoice', $args);
        if ($html) {
            $pdf_maker = get_wcmp_pdf_invoices_pdfmaker($html, $general_settings);
            $pdf = $pdf_maker->output();
            
//        ob_start();
//        $WCMp_PDF_Invoices->template->get_template($template . '.php', array('order' => $order, 'general_settings' => $general_settings, 'user_type' => 'admin', 'vendor' => ''));
//        $ob_get_clean = ob_get_clean();
            if ($pdf) {
                //$pdf_maker = get_wcmp_pdf_invoices_pdfmaker( $ob_get_clean,array() );
                file_put_contents($file_to_save, $pdf);

                update_post_meta($order_id, '_pdf_invoice_per_order', $file_to_save);
                update_post_meta($order_id, '_pdf_invoice_per_order_path', $file_to_save);
            }
        }
    }

    function create_pdf_packing_slip_per_order($order_id, $posted_data, $order) {
        
        global $WCMp_PDF_Invoices;
        $general_settings = get_option('wcmp_pdf_invoices_settings_name');
        $upload_dir = wp_upload_dir();
        $base_path2 = trailingslashit($upload_dir['basedir']) . 'wcmp_pdf_invoice/' . $order_id;
        $file_to_save = $base_path2 . '/packing_slip_' . $order_id . '.pdf';
        $vendor = get_wcmp_vendor(get_current_user_id());
        if (!file_exists($base_path2)) {
            mkdir($base_path2, 0777, true);
        }
        ob_start();
        $WCMp_PDF_Invoices->template->get_template('wcmp_packing_slip_first_template.php', array('order' => $order,  'order_id' => $order_id, 'user_type' => 'vendor','general_settings' => $general_settings));
        $ob_get_clean = ob_get_clean();
        if ($ob_get_clean) {
            $pdf_maker = get_wcmp_pdf_invoices_pdfmaker( $ob_get_clean,array() );
            file_put_contents($file_to_save, $pdf_maker->output());
            $base_url = trailingslashit($upload_dir['baseurl']) . 'wcmp_pdf_invoice/' . $order_id;
            $base_path = $upload_dir['basedir'] . '/wcmp_pdf_invoice/' . $order_id;
            $file_url = $base_url . '/packing_slip_' . $order_id . '.pdf';
            $file_path = $base_path . '/packing_slip_' . $order_id . '.pdf';
            update_post_meta($order_id, '_pdf_packing_slip_per_order', $file_url);
            update_post_meta($order_id, '_pdf_packing_slip_per_order_path', $file_path);
        }
    }


    function add_vendor_edit_invoice_report_callback($vendor, $selected_item) {
        global $WCMp, $WCMp_PDF_Invoices;
        $pages = get_option('wcmp_pages_settings_name');
        ?>
        <li><a <?php
            if ($selected_item == "vendor_edit_invoice") {
                echo 'class="selected_menu"';
            }
            ?> data-menu_item="vendor_edit_invoice" target="_blank" href="<?php echo apply_filters('wcmp_edit_vendor_pdf_invoice', get_permalink($pages['wcmp_vendor_edit_invoice'])); ?>"><?php _e('- Edit Invoice', 'wcmp-pdf_invoices'); ?></a></li>
        <?php
    }

    function frontend_scripts() {
        global $WCMp_PDF_Invoices;
        $frontend_script_path = $WCMp_PDF_Invoices->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $pluginURL = str_replace(array('http:', 'https:'), '', $WCMp_PDF_Invoices->plugin_url);
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        // Enqueue your frontend javascript from here
    }

    function frontend_styles() {
        global $WCMp_PDF_Invoices;
        $frontend_style_path = $WCMp_PDF_Invoices->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        // Enqueue your frontend stylesheet from here
    }

    function wcmp_my_account_my_orders_actions($actions, $order_id) {
        global $WCMp_PDF_Invoices;
        $options_all = get_option( 'wcmp_pdf_invoices_settings_name' );
        $get_vendor_pdf_url = add_query_arg( array('action' => 'generate_vendor_pdf_invoices_packing_slip','pdf_type' => 'invoice','vendor_id' => get_current_user_id(),'order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_vendor_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
        $pdf_action = array();
        $pdf_action['pdf_download'] = array(
            'url' => $get_vendor_pdf_url,
            'icon' => 'wcmp-font ico-pdf-icon action-icon',
            'title' => __('Download PDF Invoice', 'wcmp-pdf_invoices'),
        );
        
        $get_forcustomer_pdf_url = add_query_arg( array('action' => 'generate_vendor_pdf_invoices_packing_slip','pdf_type' => 'invoice','vendor_id' => get_current_user_id(), 'user_type' => 'customer', 'order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_vendor_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
        $pdf_action['forcustomer_pdf_download'] = array(
            'url' => $get_forcustomer_pdf_url,
            'icon' => 'wcmp-font ico-pdf-icon action-icon',
            'title' => __('Download PDF Invoice for Customer', 'wcmp-pdf_invoices'),
        );

        if(isset($options_all['is_packing_slip_customer'])) {
            $get_packing_slip_pdf_url = add_query_arg( array('action' => 'generate_vendor_pdf_invoices_packing_slip','pdf_type' => 'packing_slip','vendor_id' => get_current_user_id(),'order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_vendor_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
            
            $pdf_action['packing_slip_download'] = array(
                'url' => $get_packing_slip_pdf_url,
                'icon' => 'wcmp-font ico-pdf-icon action-icon',
                'title' => __('Download Packing Slip Invoice', 'wcmp-pdf_invoices'),
            );
        }
        $pdf_action = apply_filters('wcmp_pdf_invoices_order_vendor_actions', $pdf_action, $order_id, get_current_user_id());
        return array_merge($actions, $pdf_action);
    }

    function woocommerce_my_account_my_orders_actions($actions, $order) {
        global $WCMp;
        $wcmp_get_pdf_settings = get_wcmp_pdf_invoices_settings();
        if(isset($wcmp_get_pdf_settings['status_to_download_invoice']) && $wcmp_get_pdf_settings['status_to_download_invoice'] != 'disabled'){
            $valid_order_status = array();
            if (!empty($wcmp_get_pdf_settings['status_to_download_invoice'])) {
                $valid_order_status[] = $wcmp_get_pdf_settings['status_to_download_invoice'];
                if (in_array('wc-' . $order->get_status('edit'), $valid_order_status)) {
                    $actions['pdf_download'] = array(
                        'url' => add_query_arg( array('action' => 'generate_wcmp_pdf_invoices_packing_slip','pdf_type' => 'invoice','user_type' => 'customer','order_ids' => $order->get_id(),'_wpnonce' => wp_create_nonce('generate_wcmp_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') ),
                        'name' => __('PDF Invoice', 'wcmp-pdf_invoices')
                    );
                }
            }
        }
        return $actions;
    }

    function woocommerce_my_account_my_orders_pdf_columns( $columns ) {
        $suborder_column['wcmp_suborder_pdf'] = __( 'Suborder Invoice', 'wcmp-pdf_invoices' );
        $columns = $columns + $suborder_column ;
        return $columns;
    }

    function woocommerce_my_account_my_orders_column_wcmp_suborder_PDF( $order ){
        global $WCMp;
        $wcmp_get_pdf_settings = get_wcmp_pdf_invoices_settings();
        $valid_order_status = array();
        if ( !empty( $wcmp_get_pdf_settings['status_to_download_suborder_invoice'] ) ) {
            $valid_order_status[] = $wcmp_get_pdf_settings['status_to_download_suborder_invoice'];
            if ( in_array( 'wc-' . $order->get_status( 'edit' ), $valid_order_status )) {
                $wcmp_suborders = get_wcmp_suborders( $order->get_id() );
                if ( $wcmp_suborders ) {
                    echo '<ul class="wcmp-order-vendor" style="margin:0px;list-style:none;">';
                    foreach ( $wcmp_suborders as $suborder ) {
                        echo '<li><a href="'. esc_url(wp_nonce_url(admin_url("admin-ajax.php?action=generate_wcmp_pdf_invoices_packing_slip&pdf_type=invoice&user_type=customer&order_ids=" . $suborder->get_id()), 'generate_wcmp_pdf_invoices_packing_slip')).'"><small>PDF Invoice For </small><strong>#'. $suborder->get_id() .'</strong></a></li>';
                    do_action( 'wcmp_after_suborder_pdf_details', $suborder );
                    }
                    echo '<ul>';
                } 
            }else{
                echo '<span class="na">&ndash;</span>';
            }
        }
    }
}