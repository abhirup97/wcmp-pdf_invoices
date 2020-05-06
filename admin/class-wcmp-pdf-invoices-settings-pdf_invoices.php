<?php

class WCMp_Settings_PDF_Invoices {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $pdf_templates;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
        //add_action( 'field_end_choose_invoice_template', array($this, 'field_end_choose_invoice_template_img_sec') );
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp, $WCMp_PDF_Invoices;


        $available_emails = apply_filters('wcmp_pdf_invoice_attachment_to_email_available', array('new_order', 'cancelled_order', 'customer_processing_order', 'customer_completed_order', 'customer_invoice', 'customer_refunded_order'));
        $available_emails_filtered = array();
        if (!empty($available_emails)) {
            $available_emails_filtered['disabled'] = __('Disabled', 'wcmp-pdf_invoices');
            foreach ($available_emails as $available_email) {
                $available_emails_filtered[$available_email] = ucfirst(str_replace('_', ' ', $available_email));
            }
        }

        $template_array_options = array();
        foreach ($WCMp_PDF_Invoices->utils->get_templates_options() as $key => $value) {
            $template_array_options[$key] = $value['name'];
        }
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "header_settings_section" => array("title" => __('General Settings', 'wcmp-pdf_invoices'), // Section one
                    "fields" => array(
//                        "attach_to_email_input" => array('title' => __('Attach to Email', 'wcmp-pdf_invoices'), 'type' => 'multiinput', 'id' => 'attach_to_email_input', 'label_for' => 'attach_to_email_input', 'name' => 'attach_to_email_input', 'options' => array(// Multi Input
//                                "attach_to_email" => array('label' => __('Attach to Email', 'wcmp-pdf_invoices'), 'type' => 'select', 'id' => 'attach_to_email', 'label_for' => 'attach_to_email', 'name' => 'attach_to_email', 'options' => $available_emails_filtered, 'desc' => __('Select Order status to attach invoice.', 'wcmp-pdf_invoices')), // select
//                            ),
//                        ),
                        "pdf_output" => array('title' => __('PDF output', 'wcmp-pdf_invoices'), 'type' => 'select', 'id' => 'pdf_output', 'label_for' => 'pdf_output', 'name' => 'pdf_output', 'options' => array('download' => __('Download the PDF', 'wcmp-pdf_invoices'), 'inline' => __('Open PDF in browser', 'wcmp-pdf_invoices')), 'desc' => __('Select the pdf output mode.', 'wcmp-pdf_invoices')), // select
                        "paper_size" => array('title' => __('PDF size', 'wcmp-pdf_invoices'), 'type' => 'select', 'id' => 'paper_size', 'label_for' => 'paper_size', 'name' => 'paper_size', 'options' => array('A4' => __('A4', 'wcmp-pdf_invoices'), 'letter' => __('Letter', 'wcmp-pdf_invoices')), 'desc' => __('Select the pdf size.', 'wcmp-pdf_invoices')), // select
                        "paper_orientation" => array('title' => __('PDF orientation', 'wcmp-pdf_invoices'), 'type' => 'select', 'id' => 'paper_orientation', 'label_for' => 'paper_orientation', 'name' => 'paper_orientation', 'options' => array('portrait' => __('Portrait', 'wcmp-pdf_invoices'), 'landscape' => __('Landscape', 'wcmp-pdf_invoices')), 'desc' => __('Select the pdf orientation.', 'wcmp-pdf_invoices')), // select
                        "attach_to_email_input" => array('title' => __('Attach to email', 'wcmp-pdf_invoices'), 'type' => 'select', 'id' => 'attach_to_email_input', 'label_for' => 'attach_to_email_input', 'name' => 'attach_to_email_input', 'options' => $available_emails_filtered, 'desc' => __('Select Order status to attach invoice.', 'wcmp-pdf_invoices')), // select
                        "choose_invoice_template" => array('title' => __('Select default invoice template', 'wcmp-pdf_invoices'), 'type' => 'select', 'id' => 'choose_invoice_template', 'label_for' => 'choose_invoice_template', 'name' => 'choose_invoice_template', 'options' => $template_array_options), // select
                        "is_invoice_no" => array('title' => __('Invoice number', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_invoice_no', 'label_for' => 'is_invoice_no', 'name' => 'is_invoice_no', 'value' => 'Enable', 'hints' => __('Enable invoice number for order', 'wcmp-pdf_invoices')), // Checkbox
                        "invoice_no_format" => array('title' => __('Invoice number format', 'wcmp-pdf_invoices'), 'type' => 'text', 'id' => 'invoice_no_format', 'label_for' => 'invoice_no_format', 'name' => 'invoice_no_format', 'hints' => __('Add alphanumeric invoice number prefix or use YEAR, MONTH, \'-\' & \'\/\'.', 'wcmp-pdf_invoices'), 'desc' => __('Add alphanumeric invoice number prefix or use YEAR, MONTH, \'-\' & \'/\'.', 'wcmp-pdf_invoices')), // Text
                        //"company_name" => array('title' => __('Company name', 'wcmp-pdf_invoices'), 'type' => 'text', 'id' => 'company_name', 'label_for' => 'company_name', 'name' => 'company_name', 'hints' => __('Enter your Company Name here.', 'wcmp-pdf_invoices'), 'desc' => __('It will represent your identification.', 'wcmp-pdf_invoices')), // Text
                        "company_logo" => array('title' => __('Company logo', 'wcmp-pdf_invoices'), 'type' => 'upload', 'id' => 'company_logo', 'label_for' => 'company_logo', 'name' => 'company_logo', 'prwidth' => 125, 'hints' => __('Your presentation.', 'wcmp-pdf_invoices'), 'desc' => __('Represent your graphical signature ( JPEG Only ).', 'wcmp-pdf_invoices')), // Upload
                        //"company_address" => array('title' => __('Company address', 'wcmp-pdf_invoices'), 'type' => 'textarea', 'id' => 'company_address', 'label_for' => 'company_address', 'name' => 'company_address', 'rows' => 5, 'placeholder' => __('About you', 'wcmp-pdf_invoices'), 'desc' => __('It will represent your significant.', 'wcmp-pdf_invoices')), // Textarea
                        //"company_email" => array('title' => __('Company email', 'wcmp-pdf_invoices'), 'type' => 'text', 'id' => 'company_email', 'label_for' => 'company_email', 'name' => 'company_email', 'hints' => __('Enter your Company Email here.', 'wcmp-pdf_invoices'), 'desc' => __('It will represent your identification.', 'wcmp-pdf_invoices')), // Text
                        //"company_ph_no" => array('title' => __('Company phone number', 'wcmp-pdf_invoices'), 'type' => 'text', 'id' => 'company_ph_no', 'label_for' => 'company_ph_no', 'name' => 'company_ph_no', 'hints' => __('Enter your Company Ph no here.', 'wcmp-pdf_invoices'), 'desc' => __('It will represent your identification.', 'wcmp-pdf_invoices')), // Text
                        //"spcl_note_from_admin" => array('title' => __('Special Notes from Admin', 'wcmp-pdf_invoices'), 'type' => 'wpeditor', 'id' => 'spcl_note_from_admin', 'label_for' => 'spcl_note_from_admin', 'name' => 'spcl_note_from_admin'), //Wp Eeditor
                    )
                ),
            ),
        );

        $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_pdf_invoices_settings_sanitize($input) {
        global $WCMp_PDF_Invoices;
        $new_input = array();
        
        if (isset($input['pdf_output'])) {
            $new_input['pdf_output'] = $input['pdf_output'];
        }
        if (isset($input['paper_size'])) {
            $new_input['paper_size'] = $input['paper_size'];
        }
        if (isset($input['paper_orientation'])) {
            $new_input['paper_orientation'] = $input['paper_orientation'];
        }
        if (isset($input['is_invoice_no'])) {
            $new_input['is_invoice_no'] = $input['is_invoice_no'];
        }
        if (isset($input['invoice_no_format'])) {
        	$invoice_no_format = str_replace(' ', '-', $input['invoice_no_format']); // Replaces all spaces with hyphens.
            $new_input['invoice_no_format'] = apply_filters('wcmp_pdf_invoice_no_format_data_to_save', preg_replace('/[^A-Za-z0-9\-\/]/', '', $invoice_no_format)); // Removes special chars.
        }

        if (isset($input['choose_invoice_template'])) {
            $new_input['choose_invoice_template'] = $input['choose_invoice_template'];
        }

//        if (isset($input['company_name'])) {
//            $new_input['company_name'] = $input['company_name'];
//        }

        if (isset($input['attach_to_email_input'])) {
            $new_input['attach_to_email_input'] = $input['attach_to_email_input'];
        }

        if (isset($input['spcl_note_from_admin'])) {
            $new_input['spcl_note_from_admin'] = $input['spcl_note_from_admin'];
        }

        if (isset($input['company_logo'])) {
            $new_input['company_logo'] = $input['company_logo'];
        }

//        if (isset($input['company_address'])) {
//            $new_input['company_address'] = $input['company_address'];
//        }
//
//        if (isset($input['company_email'])) {
//            $new_input['company_email'] = $input['company_email'];
//        }
//
//        if (isset($input['company_ph_no'])) {
//            $new_input['company_ph_no'] = $input['company_ph_no'];
//        }
//
//        if (isset($input['intro_text_customer'])) {
//            $new_input['intro_text_customer'] = $input['intro_text_customer'];
//        }

//        if (isset($input['is_sku_customer'])) {
//            $new_input['is_sku_customer'] = $input['is_sku_customer'];
//        }

        if (isset($input['is_subtotal_customer'])) {
            $new_input['is_subtotal_customer'] = $input['is_subtotal_customer'];
        }

        if (isset($input['is_discount_customer'])) {
            $new_input['is_discount_customer'] = $input['is_discount_customer'];
        }

        if (isset($input['is_tax_customer'])) {
            $new_input['is_tax_customer'] = $input['is_tax_customer'];
        }

        if (isset($input['is_shipping_customer'])) {
            $new_input['is_shipping_customer'] = $input['is_shipping_customer'];
        }

        if (isset($input['is_packing_slip_customer'])) {
            $new_input['is_packing_slip_customer'] = $input['is_packing_slip_customer'];
        }

        if (isset($input['term_and_conditions_customer'])) {
            $new_input['term_and_conditions_customer'] = $input['term_and_conditions_customer'];
        }

        if (isset($input['is_customer_note_customer'])) {
            $new_input['is_customer_note_customer'] = $input['is_customer_note_customer'];
        }
        
        if (isset($input['customer_attach_to_email'])) {
            $new_input['customer_attach_to_email'] = sanitize_text_field($input['customer_attach_to_email']);
        }

        if (isset($input['status_to_download_invoice'])) {
            $new_input['status_to_download_invoice'] = $input['status_to_download_invoice'];
        }

        if (isset($input['status_to_download_suborder_invoice'])) {
            $new_input['status_to_download_suborder_invoice'] = $input['status_to_download_suborder_invoice'];
        }

        if (isset($input['intro_text_vendor'])) {
            $new_input['intro_text_vendor'] = $input['intro_text_vendor'];
        }

//        if (isset($input['is_sku_vendor'])) {
//            $new_input['is_sku_vendor'] = $input['is_sku_vendor'];
//        }

        if (isset($input['is_subtotal_vendor'])) {
            $new_input['is_subtotal_vendor'] = $input['is_subtotal_vendor'];
        }

        if (isset($input['is_discount_vendor'])) {
            $new_input['is_discount_vendor'] = $input['is_discount_vendor'];
        }

        if (isset($input['is_tax_vendor'])) {
            $new_input['is_tax_vendor'] = $input['is_tax_vendor'];
        }

        if (isset($input['is_shipping_vendor'])) {
            $new_input['is_shipping_vendor'] = $input['is_shipping_vendor'];
        }

        if (isset($input['term_and_conditions_vendor'])) {
            $new_input['term_and_conditions_vendor'] = $input['term_and_conditions_vendor'];
        }

        if (isset($input['is_customer_note_vendor'])) {
            $new_input['is_customer_note_vendor'] = $input['is_customer_note_vendor'];
        }

        if (isset($input['intro_text_admin'])) {
            $new_input['intro_text_admin'] = $input['intro_text_admin'];
        }

//        if (isset($input['is_sku_admin'])) {
//            $new_input['is_sku_admin'] = $input['is_sku_admin'];
//        }

        if (isset($input['is_subtotal_admin'])) {
            $new_input['is_subtotal_admin'] = $input['is_subtotal_admin'];
        }

        if (isset($input['is_discount_admin'])) {
            $new_input['is_discount_admin'] = $input['is_discount_admin'];
        }

        if (isset($input['is_tax_admin'])) {
            $new_input['is_tax_admin'] = $input['is_tax_admin'];
        }

        if (isset($input['is_shipping_admin'])) {
            $new_input['is_shipping_admin'] = $input['is_shipping_admin'];
        }

        if (isset($input['term_and_conditions_admin'])) {
            $new_input['term_and_conditions_admin'] = $input['term_and_conditions_admin'];
        }

        if (isset($input['is_customer_note_admin'])) {
            $new_input['is_customer_note_admin'] = $input['is_customer_note_admin'];
        }

        if (isset($input['is_payment_method_admin'])) {
            $new_input['is_payment_method_admin'] = $input['is_payment_method_admin'];
        }

        if (isset($input['is_payment_method_vendor'])) {
            $new_input['is_payment_method_vendor'] = $input['is_payment_method_vendor'];
        }

        if (isset($input['is_payment_method_customer'])) {
            $new_input['is_payment_method_customer'] = $input['is_payment_method_customer'];
        }



        return $new_input;
    }
    
//    public function field_end_choose_invoice_template_img_sec(){
//        echo '<div class="pdf_invoices_template_view_wrap">';
//        foreach ($this->pdf_templates as $key => $value) {
//            echo '<a id="'.$key.'" href="'.$value['img_url'].'" class="pdf_tpl_img thickbox" style="display:none;"><img src="'.$value['img_url'].'" height="120px"/></a>';
//        }
//        echo '</div>';
//    }

    /**
     * Print the Section text
     */
    public function header_settings_section_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your default settings below', 'wcmp-pdf_invoices');
    }

    /**
     * Print the Section text
     */
    public function body_settings_section_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your default settings below', 'wcmp-pdf_invoices');
    }

    /**
     * Print the Section text
     */
    public function footer_settings_section_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your custom settings below', 'wcmp-pdf_invoices');
    }

    /**
     * Print the Section text
     */
    public function body_settings_sectionn_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your default settings below', 'wcmp-pdf_invoices');
    }

    /**
     * Print the Section text
     */
    public function footer_settings_sectionn_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your custom settings below', 'wcmp-pdf_invoices');
    }

    /**
     * Print the Section text
     */
    public function body_settings_sectionc_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your default settings below', 'wcmp-pdf_invoices');
    }

    /**
     * Print the Section text
     */
    public function footer_settings_sectionc_info() {
        global $WCMp_PDF_Invoices;
        //_e('Enter your custom settings below', 'wcmp-pdf_invoices');
    }

}
