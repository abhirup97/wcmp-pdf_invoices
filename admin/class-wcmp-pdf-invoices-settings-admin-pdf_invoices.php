<?php

class WCMp_Settings_Admin_PDF_Invoices {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = 'pdf_invoices';
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    public function settings_page_init() {
        global $WCMp, $WCMp_PDF_Invoices;

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => new WCMp_Settings_PDF_Invoices($this->tab),
            "sections" => array(
                "body_settings_section" => array("title" => __('Admin Template Settings', 'wcmp-pdf_invoices'), // Another section
                    "fields" => array(
                        //"is_sku_admin" => array('title' => __('SKU', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_sku_admin', 'label_for' => 'is_sku_admin', 'name' => 'is_sku_admin', 'value' => 'Enable'), // Checkbox
                        "is_subtotal_admin" => array('title' => __('Subtotal', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_subtotal_admin', 'label_for' => 'is_subtotal_admin', 'name' => 'is_subtotal_admin', 'value' => 'Enable'), // Checkbox
                        "is_discount_admin" => array('title' => __('Discount', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_discount_admin', 'label_for' => 'is_discount_admin', 'name' => 'is_discount_admin', 'value' => 'Enable'), // Checkbox
                        "is_tax_admin" => array('title' => __('Tax', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_tax_admin', 'label_for' => 'is_tax_admin', 'name' => 'is_tax_admin', 'value' => 'Enable'), // Checkbox
                        "is_shipping_admin" => array('title' => __('Shipping', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_shipping_admin', 'label_for' => 'is_shipping_admin', 'name' => 'is_shipping_admin', 'value' => 'Enable'), // Checkbox
                        "is_payment_method_admin" => array('title' => __('Payment Method', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_payment_method_admin', 'label_for' => 'is_payment_method_admin', 'name' => 'is_payment_method_admin', 'value' => 'Enable'), // Checkbox
                        //"intro_text_admin" => array('title' => __('Introduction Text', 'wcmp-pdf_invoices'), 'type' => 'wpeditor', 'id' => 'intro_text_admin', 'label_for' => 'intro_text_admin', 'name' => 'intro_text_admin'), //Wp Eeditor
                        "term_and_conditions_admin" => array('title' => __('Term and conditions', 'wcmp-pdf_invoices'), 'type' => 'wpeditor', 'id' => 'term_and_conditions_admin', 'label_for' => 'term_and_conditions_admin', 'name' => 'term_and_conditions_admin', 'settings' => array('media_buttons' => false)), //Wp Eeditor
                        "is_customer_note_admin" => array('title' => __('Show Customer Note', 'wcmp-pdf_invoices'), 'type' => 'checkbox', 'id' => 'is_customer_note_admin', 'label_for' => 'is_customer_note_admin', 'name' => 'is_customer_note_admin', 'value' => 'Enable'), // Checkbox
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
        return $new_input;
    }

}

?>