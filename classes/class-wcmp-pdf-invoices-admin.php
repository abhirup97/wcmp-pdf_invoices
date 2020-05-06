<?php

class WCMP_Pdf_Invoices_Admin {

    public $settings;
    public $pdf_actions;

    public function __construct() {
        $this->pdf_actions = apply_filters('wcmp_pdf_invoice_packing_slip_bulk_actions', array(
            'invoice' => __('PDF Invoice', 'wcmp-pdf_invoices'),
            'packing_slip' => __('PDF Packing Slip', 'wcmp-pdf_invoices')
        ));
        //admin script and style
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'), 99);

        add_action('wcmp_pdf_invoices_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_wcmp_pdf_invoices'));
        add_action('add_meta_boxes_shop_order', array(&$this, 'wcmp_admin_pdf_meta_box'));
        add_action( 'save_post_shop_order', array( $this,'wcmp_save_invoice_number_date' ) );

        add_filter('wcmp_tabs', array(&$this, 'wcmp_pdf_invoices_tab'));
        add_action('settings_page_pdf_invoices_tab_init', array(&$this, 'pdf_invoices_tab_init'), 10, 1);
        add_action('wcmp_pdf_invoices_settings_before_submit', array(&$this, 'pdf_invoices_settings_subtabs'), 10);

        // pdf invoices and packing slips bulk actions
        add_filter('bulk_actions-edit-shop_order', array($this, 'register_pdf_invoice_packing_slip_bulk_actions'));
        add_filter('handle_bulk_actions-edit-shop_order', array($this, 'pdf_invoice_packing_slip_bulk_action_handler'), 10, 3);
        //add_action('admin_notices', array($this, 'subscribers_bulk_action_admin_notice'));
        add_action('woocommerce_admin_order_actions_end', array($this, 'pdf_invoice_packing_slip_row_actions'));
        add_action( 'field_end_choose_invoice_template', array($this, 'field_end_choose_invoice_template_img_sec') );
        // Update plugin modifications
        $this->do_upgrade();
    }
    
    public function field_end_choose_invoice_template_img_sec(){
        echo '<br><a target="_blank" class="wcmp-preview-pdf-tpl" href="'.add_query_arg( array('preview_pdf_template' => true, '_wpnonce' => wp_create_nonce('preview-wcmp-pdf-tpl')), admin_url() ).'" data-href="'.add_query_arg( array('preview_pdf_template' => true, '_wpnonce' => wp_create_nonce('preview-wcmp-pdf-tpl')), admin_url() ).'">'.__('Click here to see preferred template view', 'wcmp-pdf_invoices').'</a>';
    }

    function wcmp_pdf_invoices_tab($tabs) {
        global $WCMp_PDF_Invoices;
        $tabs['pdf_invoices'] = __('PDF Invoices', 'wcmp-pdf_invoices'
        );
        return $tabs;
    }

    function pdf_invoices_tab_init($tab) {
        global $WCMp, $WCMp_PDF_Invoices;
        $this->load_class("settings-{$tab}");
        new WCMp_Settings_PDF_Invoices($tab);
    }

    function pdf_invoices_settings_subtabs() {
        global $WCMp, $WCMp_PDF_Invoices;
        $tab = 'pdf_invoices';
        $this->load_class("settings-admin-{$tab}");
        new WCMp_Settings_Admin_PDF_Invoices($tab);

        $this->load_class("settings-customer-{$tab}");
        new WCMp_Settings_Customer_PDF_Invoices($tab);
    }

    function load_class($class_name = '') {
        global $WCMp_PDF_Invoices;
        if ('' != $class_name) {
            require_once ($WCMp_PDF_Invoices->plugin_path . '/admin/class-' . esc_attr($WCMp_PDF_Invoices->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WCMp_PDF_Invoices;
        $screen = get_current_screen();
        // Enqueue admin script and stylesheet from here
        if (in_array($screen->id, array('shop_order','edit-shop_order','wcmp_page_wcmp-setting-admin')) || isset($_GET['tab']) && $_GET['tab'] == 'pdf_invoices') :
            wp_enqueue_script('wcmp_pdfip_admin_js', $WCMp_PDF_Invoices->plugin_url . 'assets/admin/js/admin.js', array('jquery'), $WCMp_PDF_Invoices->version, true);
        endif;
        $custom_css = '';
        if(in_array($screen->id, array('shop_order','edit-shop_order'))) :
            $custom_css .= ".widefat .column-wc_actions a.button.generate_wcmp_pdfip img { width: 20px; }";  
            $custom_css .= ".wcmp-pdf-invoice-date-number{ float: right; } "
                    . ".wcmp_pdf_invoice_data_column .form-field .date-picker{ width:50%;}"
                    . ".wcmp_pdf_invoice_data_column .form-field label{ display:block;}"
                    . ".wcmp_pdf_invoice_data_column .form-field .hour,.wcmp_pdf_invoice_data_column .form-field .minute{ width: 3.5em;}";  
        endif;
        wp_add_inline_style( 'woocommerce_admin_styles', $custom_css );
    }

    public function wcmp_admin_pdf_meta_box() {
        global $WCMp_PDF_Invoices;
        add_meta_box(
                'wcmp-admin-pdf-invoice', __('PDF Invoice', 'wcmp-pdf_invoices'
                ), array($this, 'wcmp_pdf_invoice'), 'shop_order', 'side', 'high'
        );
        $settings = get_wcmp_pdf_invoices_settings();
        if(isset($settings['is_invoice_no'])) :
            add_meta_box(
                    'wcmp-pdf-invoice-no-date', __('PDF Invoice Number & Date', 'wcmp-pdf_invoices'
                    ), array($this, 'wcmp_pdf_invoice_no_and_date'), 'shop_order', 'side', 'high'
            );
        endif;
    }

    public function wcmp_pdf_invoice($post) {
        global $WCMp_PDF_Invoices, $post;
        $html = '<ul class="wcmp-pdf-for">';
        $html .= '<li><label><input type="radio" name="pdf_invoices_for" data-for="order" data-order_id="'.$post->ID.'" value="'.$post->ID.'"/>' . __('For order', 'wcmp-pdf_invoices') . '</label></li>';
        $vendors_in_order = wcmp_get_vendor_items_from_order($post->ID);
        if (!empty($vendors_in_order)) {
            foreach ($vendors_in_order as $vendor) {
                $html .= '<li><label><input type="radio" name="pdf_invoices_for" data-for="vendor" data-order_id="'.$post->ID.'" value="'.$vendor->id.'"/>' . __('For', 'wcmp-pdf_invoices') .' '. $vendor->user_data->data->display_name. '</label></li>';
            }
        }
        if(isset($this->pdf_actions) && is_array($this->pdf_actions)){
            foreach ($this->pdf_actions as $key => $action){
                $html .= '<li><a id="order_'.$key.'_generate" href="'. esc_url(wp_nonce_url(admin_url("admin-ajax.php?action=generate_wcmp_pdf_invoices_packing_slip&pdf_type={$key}&order_ids=" . $post->ID), 'generate_wcmp_pdf_invoices_packing_slip')).'" target="_blank" class="button" >' . $action . '</a></li>';
            }
        }
        
        $html .= '</ul>';
        echo $html;
    }
    
    public function wcmp_pdf_invoice_no_and_date($post) {
        global $WCMp_PDF_Invoices;
        $order_type_object = get_post_type_object( $post->post_type );
        $invoices = get_post_meta($post->ID, '_wcmp_pdf_invoice_number_date', true);
        ?>
        <div class="wcmp_pdf_invoice_id_meta_wrapper">
            <div class="wcmp_pdf_invoice_data_column">
                
        <?php if($invoices){ 
            krsort($invoices); // sorting by order first
            foreach ($invoices as $user_or_order_id => $invoice) {
                if(is_user_wcmp_vendor($user_or_order_id)){
                    $vendor = get_wcmp_vendor($user_or_order_id); ?>
                    <h3><?php echo $vendor->page_title; ?><span title="<?php _e( 'Edit Invoice', 'wcmp-pdf_invoices' ) ?>" data-section_id="<?php echo $user_or_order_id; ?>" class="wcmp-pdf-invoice-date-number dashicons dashicons-edit"></span></h3>
                <?php }else{ ?>
                    <h3><?php printf(
                            esc_html__( '%1$s #%2$s details', 'wcmp-pdf_invoices' ),
                            esc_html( $order_type_object->labels->singular_name ),
                            esc_html( $post->ID )
                    ); ?><span title="<?php _e( 'Edit Invoice', 'wcmp-pdf_invoices' ) ?>" data-section_id="<?php echo $user_or_order_id; ?>" class="wcmp-pdf-invoice-date-number dashicons dashicons-edit"></span></h3>
                <?php }?>
                    <p class="wcmp-invoice-field-<?php echo $user_or_order_id; ?> form-field form-field-wide">
                        <label for="wcmp_invoice_no"><?php _e( 'Invoice Number:', 'wcmp-pdf_invoices' ) ?></label>
                        <input type="text" class="short wcmp_invoice_number" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][invoice_no]" value="<?php echo esc_attr( $invoice['invoice_no'] ); ?>"/>
                    </p>
                    <p class="wcmp-invoice-field-<?php echo $user_or_order_id; ?> form-field form-field-wide"><label for="wcmp_invoice_date"><?php _e( 'Invoice Date:', 'wcmp-pdf_invoices' ) ?></label>
                        <input type="text" class="date-picker" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][date]" maxlength="10" value="<?php echo esc_attr( date_i18n( 'Y-m-d', strtotime( $invoice['invoice_date']->date ) ) ); ?>" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />@
                        &lrm;
                        <input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'wcmp-pdf_invoices' ) ?>" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][hour]" min="0" max="23" step="1" value="<?php echo esc_attr( date_i18n( 'H', strtotime( $invoice['invoice_date']->date ) ) ); ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:
                        <input type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'wcmp-pdf_invoices' ) ?>" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][minute]" min="0" max="59" step="1" value="<?php echo esc_attr( date_i18n( 'i', strtotime( $invoice['invoice_date']->date ) ) ); ?>" pattern="[0-5]{1}[0-9]{1}" />
                        <input type="hidden" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][second]" value="<?php echo esc_attr( date_i18n( 's', strtotime( $invoice['invoice_date']->date ) ) ); ?>" />
                        &lrm;
                    </p>
                <?php 
            }
        } else {
            $order = wc_get_order($post->ID);
            $line_items = $order->get_items();
            $invoice_for = array();
            $invoice_for[$post->ID] = array();
            foreach ( $line_items as $item_id => $item ) {
                $product = $item->get_product();
                $have_vendor = get_wcmp_product_vendors($product->get_id());
                if($have_vendor){
                    $invoice_for[$have_vendor->id] = array();
                }
            }
            krsort($invoice_for); // sorting by order first
            foreach ($invoice_for as $user_or_order_id => $invoice) {
                if(is_user_wcmp_vendor($user_or_order_id)){
                    $vendor = get_wcmp_vendor($user_or_order_id); ?>
                <h3><?php echo $vendor->page_title; ?><span title="<?php _e( 'Edit Invoice', 'wcmp-pdf_invoices' ) ?>" data-section_id="<?php echo $user_or_order_id; ?>" class="wcmp-pdf-invoice-date-number dashicons dashicons-edit"></span></h3>
                <?php }else{ ?>
                <h3><?php printf(
                        esc_html__( '%1$s #%2$s details', 'wcmp-pdf_invoices' ),
                        esc_html( $order_type_object->labels->singular_name ),
                        esc_html( $post->ID )
                ); ?><span title="<?php _e( 'Edit Invoice', 'wcmp-pdf_invoices' ) ?>" data-section_id="<?php echo $user_or_order_id; ?>" class="wcmp-pdf-invoice-date-number dashicons dashicons-edit"></span></h3>
                <?php }?>
                <p class="wcmp-invoice-field-<?php echo $user_or_order_id; ?> form-field form-field-wide">
                    <label for="wcmp_invoice_no"><?php _e( 'Invoice Number:', 'wcmp-pdf_invoices' ) ?></label>
                    <input type="text" class="short wcmp_invoice_number" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][invoice_no]" value=""/>
                </p>
                <p class="wcmp-invoice-field-<?php echo $user_or_order_id; ?> form-field form-field-wide"><label for="wcmp_invoice_date"><?php _e( 'Invoice Date:', 'wcmp-pdf_invoices' ) ?></label>
                    <input type="text" class="date-picker" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][date]" maxlength="10" value="" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />@
                    &lrm;
                    <input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'wcmp-pdf_invoices' ) ?>" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][hour]" min="0" max="23" step="1" value="" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:
                    <input type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'wcmp-pdf_invoices' ) ?>" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][minute]" min="0" max="59" step="1" value="" pattern="[0-5]{1}[0-9]{1}" />
                    <input type="hidden" name="wcmp_invoice_number_date[<?php echo $user_or_order_id; ?>][second]" value="" />
                    &lrm;
                </p>
                <?php 
            }
        } ?>
            </div>
        </div>
        <?php
        
    }
    
    /**
     * Save pdf invoice number and date
     */
    public function wcmp_save_invoice_number_date($post_id) {
        global $WCMp_PDF_Invoices;
        
        $post_type = get_post_type( $post_id );
        $time = current_time( 'timestamp', true );
        $general_settings = get_wcmp_pdf_invoices_settings();
        $already_have_invoice_number_date = get_post_meta($post_id, '_wcmp_pdf_invoice_number_date', true) ? get_post_meta($post_id, '_wcmp_pdf_invoice_number_date', true) : array();
        if(isset($general_settings['is_invoice_no']) && !$already_have_invoice_number_date && !isset($_POST['wcmp_invoice_number_date'])){
            $order = wc_get_order($post_id);
            $line_items = $order->get_items();
            $invoice_number_date = array();
            
            if($order){
                $invoice_number_date[$post_id] = array(
                    'invoice_no' => $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $post_id . $time,
                    'invoice_date' => $WCMp_PDF_Invoices->utils->set_date($time)
                );
            }
            foreach ( $line_items as $item_id => $item ) {
                $product = $item->get_product();
                $time = current_time( 'timestamp', true );
                $have_vendor = get_wcmp_product_vendors($product->get_id());
                if($have_vendor){
                    $invoice_number_date[$have_vendor->id] = array(
                        'invoice_no' => $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $have_vendor->id . $time,
                        'invoice_date' => $WCMp_PDF_Invoices->utils->set_date($time)
                    );
                }
            }
            update_post_meta($post_id, '_wcmp_pdf_invoice_number_date', $invoice_number_date);
        }elseif(isset($_POST['wcmp_invoice_number_date'])){
            $invoices_data = is_array($_POST['wcmp_invoice_number_date']) ? $_POST['wcmp_invoice_number_date'] : array();
            $order = wc_get_order($post_id);
            $line_items = $order->get_items();
            $invoice_number_date = array();
            if($order){
                if(!empty($invoices_data[$post_id]['invoice_no'])) $invoice_number_date[$post_id]['invoice_no'] = $invoices_data[$post_id]['invoice_no'];
                else $invoice_number_date[$post_id]['invoice_no'] = $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $post_id . $time;
                
                $date = !empty($invoices_data[$post_id]['date']) ? $invoices_data[$post_id]['date'] : date( 'Y-m-d' );
                $hour = !empty($invoices_data[$post_id]['hour']) ? $invoices_data[$post_id]['hour'] : '00';
                $minute = !empty($invoices_data[$post_id]['minute']) ? $invoices_data[$post_id]['minute'] : '00';
                $second = !empty($invoices_data[$post_id]['second']) ? $invoices_data[$post_id]['second'] : '00';
                
                if ( empty($date) ) {
                    $invoice_number_date[$post_id]['invoice_date'] = $WCMp_PDF_Invoices->utils->set_date(current_time( 'timestamp', true ));
                } else {
                    $invoice_number_date[$post_id]['invoice_date'] = $WCMp_PDF_Invoices->utils->set_date(strtotime($date . ' ' . (int) $hour . ':' . (int) $minute . ':' . (int) $second));
                }
            }
            foreach ( $line_items as $item_id => $item ) {
            	$product = $item->get_product();
                $time = current_time( 'timestamp', true );
                $have_vendor = get_wcmp_product_vendors($product->get_id());
                if($have_vendor){
                	if(!empty($invoices_data[$have_vendor->id]['invoice_no'])) $invoice_number_date[$have_vendor->id]['invoice_no'] = $invoices_data[$have_vendor->id]['invoice_no'];
					else $invoice_number_date[$have_vendor->id]['invoice_no'] = $WCMp_PDF_Invoices->utils->get_invoice_prefix() . $have_vendor->id . $time;
					
					$date = !empty($invoices_data[$have_vendor->id]['date']) ? $invoices_data[$have_vendor->id]['date'] : date( 'Y-m-d' );
					$hour = !empty($invoices_data[$have_vendor->id]['hour']) ? $invoices_data[$have_vendor->id]['hour'] : '00';
					$minute = !empty($invoices_data[$have_vendor->id]['minute']) ? $invoices_data[$have_vendor->id]['minute'] : '00';
					$second = !empty($invoices_data[$have_vendor->id]['second']) ? $invoices_data[$have_vendor->id]['second'] : '00';
					
					if ( empty($date) ) {
						$invoice_number_date[$have_vendor->id]['invoice_date'] = $WCMp_PDF_Invoices->utils->set_date(current_time( 'timestamp', true ));
					} else {
						$invoice_number_date[$have_vendor->id]['invoice_date'] = $WCMp_PDF_Invoices->utils->set_date(strtotime($date . ' ' . (int) $hour . ':' . (int) $minute . ':' . (int) $second));
					}	
                }
            }
            
            update_post_meta($post_id, '_wcmp_pdf_invoice_number_date', $invoice_number_date);
        }
    }
    
    /**
     * Add PDF bulk actions to the orders listing
     */
    public function register_pdf_invoice_packing_slip_bulk_actions($bulk_actions) {
        if(isset($this->pdf_actions) && is_array($this->pdf_actions)){
            return array_merge($bulk_actions, $this->pdf_actions);
        }

        return $bulk_actions;
    }
    
    /**
     * handle PDF invoice & packing slip bulk action
     */
    public function pdf_invoice_packing_slip_bulk_action_handler($redirect_to, $doaction, $order_ids) {
        global $WCMp_PDF_Invoices;
        
        if (!array_key_exists($doaction, $this->pdf_actions)){
            return $redirect_to;
        }
        if (empty($order_ids)) { 
            return $redirect_to;
        }
        
        $general_settings = get_wcmp_pdf_invoices_settings();
        $args = array(
            'order_ids' => $order_ids, 
            'user_id' => get_current_user_id(), 
            'user_type' => 'admin',
            'settings' => $general_settings
        );
        $html = $WCMp_PDF_Invoices->utils->get_html($doaction, $args);
        
        if ($html) {
            $pdf_maker = get_wcmp_pdf_invoices_pdfmaker( $html, $general_settings );
            $pdf = $pdf_maker->output();
            wcmp_pdf_invoices_pdf_headers( $doaction.'.pdf', $general_settings['pdf_output'], $pdf );
            echo $pdf;
            die();
        }
        exit;
    }

    /**
     * Add PDF actions to the orders listing
     */
    public function pdf_invoice_packing_slip_row_actions($order) {
        global $WCMp_PDF_Invoices;
        // do not show buttons for trashed orders
        if ($order->get_status() == 'trash') {
            return;
        }

        $pdf_row_actions = array();
        if(isset($this->pdf_actions) && is_array($this->pdf_actions)){
            foreach ($this->pdf_actions as $key => $action){
                $pdf_row_actions[$key] = array(
                    'url' => wp_nonce_url(admin_url("admin-ajax.php?action=generate_wcmp_pdf_invoices_packing_slip&pdf_type={$key}&order_ids=" . $order->get_id()), 'generate_wcmp_pdf_invoices_packing_slip'),
                    'img' => $WCMp_PDF_Invoices->plugin_url . "/assets/images/pdf.png",
                    'alt' => $action,
                );
            }
        }

        $pdf_row_actions = apply_filters('wcmp_pdf_invoice_packing_slip_row_actions', $pdf_row_actions, $order);

        foreach ($pdf_row_actions as $action => $data) {
            ?>
            <a href="<?php echo $data['url']; ?>" class="button tips generate_wcmp_pdfip <?php echo $action; ?>" target="_blank" alt="<?php echo $data['alt']; ?>" data-tip="<?php echo $data['alt']; ?>">
                <img src="<?php echo $data['img']; ?>" alt="<?php echo $data['alt']; ?>" width="16">
            </a>
            <?php
        }
    }
    
    public function do_upgrade(){
        global $WCMp_PDF_Invoices;
        // sync fonts on every upgrade!
        $tmp_base = $WCMp_PDF_Invoices->utils->init_wcmp_pdf_tmp_base();
        // check if tmp folder exists => if not, initialize 
        if ( $tmp_base !== false && !@is_dir( $tmp_base ) ) {
            $WCMp_PDF_Invoices->utils->init_wcmp_pdf_tmp( $tmp_base );
        } else {
            $font_path = $WCMp_PDF_Invoices->utils->get_wcmp_pdf_tmp_path( 'fonts' );
            $WCMp_PDF_Invoices->utils->copy_dompdf_fonts_to_local( $font_path, false );
        }
    }

}
