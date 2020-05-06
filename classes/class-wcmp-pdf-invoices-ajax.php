<?php

class WCMP_Pdf_Invoices_Ajax {

    public function __construct() {
        // PDF invoice & packing slips
        add_action('wp_ajax_generate_wcmp_pdf_invoices_packing_slip', array(&$this, 'generate_wcmp_pdf_invoices_packing_slip'));
        add_action('wp_ajax_wcmp_change_order_pdf_generate_url', array(&$this, 'wcmp_change_order_pdf_generate_url'));
        add_action('wp_ajax_generate_vendor_pdf_invoices_packing_slip', array(&$this, 'generate_vendor_pdf_invoices_packing_slip'));
    }

    public function generate_wcmp_pdf_invoices_packing_slip() {
        global $WCMp_PDF_Invoices;
        // Check the nonce
        if ((empty($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'generate_wcmp_pdf_invoices_packing_slip')) && empty($_GET['pdf_type'])) {
            wp_die(__('Some of the PDF export parameters are missing.', 'wcmp-pdf_invoices'));
        }
        if (empty($_GET['action']) || !check_admin_referer($_GET['action'])) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wcmp-pdf_invoices'));
        }

        $pdf_type = sanitize_text_field($_GET['pdf_type']);
        $order_ids = $_GET['order_ids'];
        $user_type = isset($_GET['user_type']) ? sanitize_text_field($_GET['user_type']) : 'admin';

        $general_settings = get_wcmp_pdf_invoices_settings();

        if ($pdf_type == 'invoice') {
            $template = isset($general_settings['choose_invoice_template']) ? $general_settings['choose_invoice_template'] : 'wcmp_pdf_invoice_first_template';
        } else {
            $template = 'wcmp_packing_slip_first_template';
        }
        // here, we're safe to go!

        $order = wc_get_order(absint($order_ids));
        try {
            $html = $WCMp_PDF_Invoices->utils->get_html($pdf_type, array('order_ids' => $order_ids, 'general_settings' => $general_settings, 'user_type' => $user_type));
            if ($html) { 
                $pdf_maker = get_wcmp_pdf_invoices_pdfmaker($html, $general_settings);
                $pdf = $pdf_maker->output();
                $file_name = $WCMp_PDF_Invoices->utils->get_filename($pdf_type, $order_ids );
                wcmp_pdf_invoices_pdf_headers($file_name, $general_settings['pdf_output'], $pdf);
                echo $pdf;
                die();
            } else {
                wp_die(__("PDF document could not be generated", 'wcmp-pdf_invoices'));
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        exit;
    }

    public function wcmp_change_order_pdf_generate_url() {
        global $WCMp_PDF_Invoices;
        $pdf_for = isset($_POST['pdf_for']) ? $_POST['pdf_for'] : 'order';
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
        $order_id_or_vendor_id = isset($_POST['order_id_or_vendor_id']) ? $_POST['order_id_or_vendor_id'] : '';
        $general_settings = get_wcmp_pdf_invoices_settings();
        if ($pdf_for == 'vendor') {
            $vendor_id = $order_id_or_vendor_id;
            $invoice_url = add_query_arg( array('action' => 'generate_vendor_pdf_invoices_packing_slip','pdf_type' => 'invoice','vendor_id' => $vendor_id,'order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_vendor_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
            $packing_slip = add_query_arg( array('action' => 'generate_vendor_pdf_invoices_packing_slip','pdf_type' => 'packing_slip','vendor_id' => $vendor_id,'order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_vendor_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
        } else {
            $order_id = $order_id_or_vendor_id;
            $invoice_url = add_query_arg( array('action' => 'generate_wcmp_pdf_invoices_packing_slip','pdf_type' => 'invoice','order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_wcmp_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
            $packing_slip = add_query_arg( array('action' => 'generate_wcmp_pdf_invoices_packing_slip','pdf_type' => 'packing_slip','order_ids' => $order_id,'_wpnonce' => wp_create_nonce('generate_wcmp_pdf_invoices_packing_slip')), admin_url('admin-ajax.php') );
        }
        wp_send_json(array('invoice_url' => $invoice_url, 'packing_slip_url' => $packing_slip));
        die;
    }

    public function generate_vendor_pdf_invoices_packing_slip() {
        global $WCMp_PDF_Invoices;
        // Check the nonce
        if ((empty($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'generate_vendor_pdf_invoices_packing_slip')) && empty($_GET['pdf_type'])) {
            wp_die(__('Some of the PDF export parameters are missing.', 'wcmp-pdf_invoices'));
        }
        if (empty($_GET['action']) || !check_admin_referer($_GET['action'])) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wcmp-pdf_invoices'));
        }

        $pdf_type = sanitize_text_field($_GET['pdf_type']);
        $order_ids = isset($_GET['order_ids']) ? $_GET['order_ids'] : 0;
        $vendor_id = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : 0;
        $user_type = isset($_GET['user_type']) ? $_GET['user_type'] : 'vendor';
        $vendor = get_wcmp_vendor($vendor_id);
        if (!$vendor) {
            wp_die(__('The user is not a vendor.', 'wcmp-pdf_invoices'));
        }
        $general_settings = get_wcmp_pdf_invoices_settings($vendor->id);
        
        if ($pdf_type == 'invoice') {
            $template = isset($general_settings['choose_invoice_template']) ? $general_settings['choose_invoice_template'] : 'wcmp_pdf_invoice_first_template';
        } else {
            $template = 'wcmp_packing_slip_first_template';
        }
        // here, we're safe to go!

        $order = wc_get_order(absint($order_ids));
        try {
            $args = array(
                'order_ids' => $order_ids, 
                'user_id' => $vendor_id, 
                'user_type' => $user_type,
                'settings' => $general_settings
            );
            
            $file_name = $WCMp_PDF_Invoices->utils->get_filename($pdf_type, $order_ids, $vendor_id, $args );
            $html = $WCMp_PDF_Invoices->utils->get_html($pdf_type, $args);

            if ($html) {
                $pdf_maker = get_wcmp_pdf_invoices_pdfmaker($html, $general_settings);
                $pdf = $pdf_maker->output();
                wcmp_pdf_invoices_pdf_headers($file_name, $general_settings['pdf_output'], $pdf);
                echo $pdf;
                die();
            } else {
                wp_die(__("PDF document could not be generated", 'wcmp-pdf_invoices'));
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        exit;
    }

}
