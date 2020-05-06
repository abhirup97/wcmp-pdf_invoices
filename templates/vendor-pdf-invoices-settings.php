<?php
/**
 * The template for displaying vendor pdf invoices settings
 *
 * Override this template by copying it to yourtheme/wcmp-pdf-invoices/vendor-pdf-invoices-settings.php
 * @version   2.0.3
 */
global $WCMp_PDF_Invoices, $WCMp;
$vendor = get_current_vendor();
$settings = get_user_meta($vendor->id, 'wcmp_pdf_invoices_settings', true);
$_wp_editor_settings = array('tinymce' => true);
if (!$WCMp->vendor_caps->vendor_can('is_upload_files')) {
    $_wp_editor_settings['media_buttons'] = false;
}
$_wp_editor_settings = apply_filters('wcmp_vendor_pdf_invoices_settings_wp_editor_settings', $_wp_editor_settings);
$template_array = $WCMp_PDF_Invoices->utils->get_templates_options();
?>
<div class="col-md-12">
    <form method="post" name="vendor_frontent_invoice_edit_settings" class="wcmp_pdf_invoices_form form-horizontal">
            <div class="panel panel-default pannel-outer-heading">
                <div class="panel-heading">
                    <h3><?php _e('Invoices Settings', 'wcmp-pdf_invoices'); ?></h3>
                </div>
                <div class="panel-body panel-content-padding">
                    <?php wp_nonce_field( 'wcmp_vendor_pdf_invoices_settings', 'pdf_invoices_nonce' ); ?>
                    <?php do_action('before_wcmp_pdf_invoices_settings_field'); ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Select your preferred template', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <select class="vendor_preferred_template form-control regular-select" name="choose_invoice_template">
                            <?php foreach ($template_array as $template_array_key => $template_array_val) { ?>
                                <option value="<?php echo $template_array_key; ?>" <?php selected(get_wcmp_pdf_invoices_settings($vendor->id, 'choose_invoice_template'), $template_array_key); ?> data-tpl_img="<?php echo $template_array_val['img_url']; ?>" ><?php echo $template_array_val['name']; ?></option>
                                <?php }
                            ?>
                            </select>
                            <a target="_blank" class="wcmp-preview-pdf-tpl" href="<?php echo add_query_arg( array('preview_pdf_template' => true, '_wpnonce' => wp_create_nonce('preview-wcmp-pdf-tpl')), get_permalink(wcmp_vendor_dashboard_page_id()) );?>" data-href="<?php echo add_query_arg( array('preview_pdf_template' => true, '_wpnonce' => wp_create_nonce('preview-wcmp-pdf-tpl')), get_permalink(wcmp_vendor_dashboard_page_id()) );?>"><?php _e('Click here to see preferred template view', 'wcmp-pdf_invoices'); ?></a>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Logo ( JPEG Only)', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <span class="dc-wp-fields-uploader">
                                <img id="vendor_invoice_logo_display" src="<?php echo get_wcmp_pdf_invoices_settings($vendor->id, 'vendor_invoice_logo'); ?>" width="75" class="">
                                <input type="hidden" name="vendor_invoice_logo" id="vendor_invoice_logo" value="<?php echo get_wcmp_pdf_invoices_settings($vendor->id, 'vendor_invoice_logo'); ?>" data-mime="image">
                                <input type="button" class="upload_button button button-secondary" name="vendor_invoice_logo_button" id="vendor_invoice_logo_button" data-mime="image" value="<?php _e('Upload', 'wcmp-pdf_invoices'); ?>">
                                <input type="button" class="remove_button button button-secondary" name="vendor_invoice_logo_remove_button" id="vendor_invoice_logo_remove_button" data-mime="image" value="<?php _e('Remove', 'wcmp-pdf_invoices'); ?>">
                            </span>
                        </div>  
                    </div>
                    <!--div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('SKU', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_sku_vendor" name="is_sku_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_sku_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div-->
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Subtotal', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_subtotal_vendor" name="is_subtotal_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_subtotal_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Discount', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_discount_vendor" name="is_discount_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_discount_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Tax', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_tax_vendor" name="is_tax_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_tax_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Shipping', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_shipping_vendor" name="is_shipping_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_shipping_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Show Payment Method', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_payment_method_vendor" name="is_payment_method_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_payment_method_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div>
                    <!--div class="form-group">
                        <label class="control-label col-sm-3"><?php //_e('Introduction Text', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <?php //wp_editor(get_wcmp_pdf_invoices_settings($vendor->id, 'intro_text_vendor'), 'listingeditor1', array_merge(array('textarea_name' => 'intro_text_vendor', 'textarea_rows' => 5), $_wp_editor_settings)); ?>
                        </div>  
                    </div-->
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Term and conditions', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <?php wp_editor(get_wcmp_pdf_invoices_settings($vendor->id, 'term_and_conditions_vendor'), 'listingeditor2', array_merge(array('textarea_name' => 'term_and_conditions_vendor', 'textarea_rows' => 5), $_wp_editor_settings)); ?>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Show Customer Note', 'wcmp-pdf_invoices'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type="checkbox" id="is_customer_note_vendor" name="is_customer_note_vendor" class="checkbox" value="Enable" <?php checked( get_wcmp_pdf_invoices_settings($vendor->id, 'is_customer_note_vendor'), 'Enable' ); ?> >
                        </div>  
                    </div>
                    <?php do_action('after_wcmp_pdf_invoices_settings_field', $vendor->id, $settings); ?>
                </div>
            </div>
        <div class="wcmp-action-container">
            <button class="btn btn-default" name="store_save_policy"><?php _e('Save Options', 'wcmp-pdf_invoices'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>