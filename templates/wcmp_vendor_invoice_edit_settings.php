<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/wcmp-pdf-invoices/wcmp_vendor_invoice_edit_settings.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp PDF Invoices/Templates
 * @version     2.0.3
 */

global $WCMp_PDF_Invoices;

$template_array = apply_filters('wcmp_pdf_invoice_preferred_templates', array(
    'wcmp_pdf_invoice_first_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template1.jpg', 'name' => __('Template 1', 'wcmp-pdf_invoices')),
    'wcmp_pdf_invoice_second_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template2.jpg', 'name' => __('Template 2', 'wcmp-pdf_invoices')),
    'wcmp_pdf_invoice_third_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template3.jpg', 'name' => __('Template 3', 'wcmp-pdf_invoices')),
    'wcmp_pdf_invoice_forth_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template4.jpg', 'name' => __('Template 4', 'wcmp-pdf_invoices')),
    'wcmp_pdf_invoice_fifth_template' => array('img_url' => $WCMp_PDF_Invoices->plugin_url . 'assets/images/template5.jpg', 'name' => __('Template 5', 'wcmp-pdf_invoices')),
));
?>
<form name="vendor_frontent_invoice_edit_settings" method="post" >
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label for="is_vendor_logo"><?php _e('Select your preferred template', 'wcmp-pdf_invoices'); ?></label></th>
                <td>
                    <select class="vendor_preferred_template" name="choose_preferred_template">
                        <?php foreach ($template_array as $template_array_key => $template_array_val) { ?>
                            <option value = "<?php echo $template_array_key; ?>" <?php if ($settings['choose_preferred_template'] != '' && $settings['choose_preferred_template'] == $template_array_key) echo 'selected = "selected"'; ?> data-id = "<?php echo $template_array_val['img_url']; ?>" ><?php echo $template_array_val['name']; ?></option>
                            <?php }
                        ?>
                    </select>
                </td>
                <td>
                    <?php if (isset($template_array[$settings['choose_preferred_template']]['img_url'])) : ?>
                        <img src="<?php echo $template_array[$settings['choose_preferred_template']]['img_url']; ?>" alt="vendor_choosed_template_view" class="vendor_choosed_template_view"/>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
            <?php do_action('before_wcmp_pdf_invoices_settings_field'); ?>
            <tr>
                <th scope="row"><label for="is_vendor_logo"><?php _e('Logo ( JPEG Only)', 'wcmp-pdf_invoices'); ?></label></th>
                <td>
                    <span class="dc-wp-fields-uploader">
                        <img id="vendor_banner_display" width="300" src="<?php if ($settings['vendor_invoice_logo'] != '') echo $settings['vendor_invoice_logo']; ?>" class="placeHolder" />
                        <input type="text" name="vendor_invoice_logo" id="vendor_banner" style="display: none;" class="user-profile-fields" readonly value="<?php if ($settings['vendor_invoice_logo'] != '') echo $settings['vendor_invoice_logo']; ?>"  />
                        <input type="button" class="upload_button button button-secondary" name="vendor_banner_button" id="vendor_banner_button" value="Upload" />
                        <input type="button" class="remove_button button button-secondary" name="vendor_banner_remove_button" id="vendor_banner_remove_button" value="Remove" />
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="is_sku_vendor"><?php _e('SKU', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_sku_vendor" name="is_sku_vendor" class="checkbox" value="Enable" <?php if ($settings['is_sku_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <th scope="row"><label for="is_subtotal_vendor"><?php _e('Subtotal', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_subtotal_vendor" name="is_subtotal_vendor" class="checkbox" value="Enable" <?php if ($settings['is_subtotal_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <th scope="row"><label for="is_discount_vendor"><?php _e('Discount', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_discount_vendor" name="is_discount_vendor" class="checkbox" value="Enable" <?php if ($settings['is_discount_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <th scope="row"><label for="is_tax_vendor"><?php _e('Tax', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_tax_vendor" name="is_tax_vendor" class="checkbox" value="Enable" <?php if ($settings['is_tax_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <th scope="row"><label for="is_shipping_vendor"><?php _e('Shipping', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_shipping_vendor" name="is_shipping_vendor" class="checkbox" value="Enable" <?php if ($settings['is_shipping_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <th scope="row"><label for="is_payment_method_vendor"><?php _e('Show Payment Method', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_payment_method_vendor" name="is_payment_method_vendor" class="checkbox" value="Enable" <?php if ($settings['is_payment_method_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <th scope="row"><label for="intro_text_vendor"><?php _e('Introduction Text', 'wcmp-pdf_invoices'); ?></label></th>
                <td><?php wp_editor($settings['intro_text_vendor'], 'listingeditor1', array('textarea_name' => 'intro_text_vendor', 'textarea_rows' => 5)); ?></td>
            </tr>
            <tr>
                <th scope="row"><label for="term_and_conditions_vendor"><?php _e('Term and conditions', 'wcmp-pdf_invoices'); ?></label></th>
                <td><?php wp_editor($settings['term_and_conditions_vendor'], 'listingeditor2', array('textarea_name' => 'term_and_conditions_vendor', 'textarea_rows' => 5)); ?></td>
            </tr>
<!--			<tr>
                    <th scope="row"><label for="spcl_notes_from_vendor">Special Note</label></th>
                    <td><?php wp_editor($settings['spcl_notes_from_vendor'], 'listingeditor3', array('textarea_name' => 'spcl_notes_from_vendor', 'textarea_rows' => 5)); ?></td>
            </tr>-->
            <tr>
                <th scope="row"><label for="is_customer_note_vendor"><?php _e('Show Customer Note', 'wcmp-pdf_invoices'); ?></label></th>
                <td><input type="checkbox" id="is_customer_note_vendor" name="is_customer_note_vendor" class="checkbox" value="Enable" <?php if ($settings['is_customer_note_vendor'] != '') echo 'checked="checked"'; ?> ></td>
            </tr>
            <?php do_action('after_wcmp_pdf_invoices_settings_field'); ?>
        </tbody>
    </table>
    <p><input type="submit" value="Save" name="save_vendor_invoice_settings"></p>
</form>
