<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/wcmp-pdf-invoices/wcmp_pdf_invoice_fifth_template.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp PDF Invoices/Templates
 * @version     2.0.3
 */

global $WCMp_PDF_Invoices;
$invoice_data = $WCMp_PDF_Invoices->utils->get_invoice($order->get_id(),$user_id);
$hook_data = array(
    'order' => $order,
    'template' => $template,
    'pdf_type' => $pdf_type,
    'user_id' => $user_id, 
    'user_type' => $user_type,
    'settings' => $settings,
);
?>
<div class="invoice5">
    <?php do_action( 'before_wcmp_pdf_invoice_template', $hook_data ); ?>
    <table cellpadding="0" cellspacing="0" width="100%" class="invoice-detail">
        <tr>
            <td width="65%" class="invoice-info">
                <?php echo $WCMp_PDF_Invoices->utils->header_logo($user_id) ?>
                <table align="right">
                    <tbody>
                        <?php do_action('before_wcmp_pdf_invoice_order_invoice_details', $hook_data); ?>
                        <?php $is_invoice_enable = get_wcmp_pdf_invoices_settings($user_id, 'is_invoice_no'); if($is_invoice_enable == 'Enable' && $invoice_data) : ?>
                        <tr>
                            <td><?php _e('Invoice no:', 'wcmp-pdf_invoices'); ?></td>
                            <td><?php echo $invoice_data['invoice_no']; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e('Invoice date:', 'wcmp-pdf_invoices'); ?></td>
                            <td><?php echo date_i18n(wc_date_format(), strtotime($invoice_data['invoice_date']->date)); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><?php _e('Order no:', 'wcmp-pdf_invoices'); ?></td>
                            <td>#<?php echo $order->get_id(); ?></td>
                        </tr>
                        <tr>
                            <td><?php _e('Order date:', 'wcmp-pdf_invoices'); ?></td>
                            <td><?php echo date_i18n(wc_date_format(), strtotime($order->get_date_created())); ?></td>
                        </tr>
                        
                
                        <?php $key = 'is_payment_method_'.$user_type; if(isset($settings[$key]) && $settings[$key] == 'Enable') : ?>
                        <tr>
                            <td><?php _e('Payment method:', 'wcmp-pdf_invoices'); ?></td>
                            <td><?php echo $order->get_payment_method_title(); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php do_action('after_wcmp_pdf_invoice_order_invoice_details', $hook_data); ?>
                    </tbody>
                </table>
            </td>
            <td width="42%" style=" padding-top: 40px; ">
                <?php do_action('before_wcmp_pdf_invoice_to_address_details', $hook_data); if($user_type == 'customer'){
                    ?>
                    <p class="invoice-text"><strong><?php _e('Sold By:', 'wcmp-pdf_invoices'); ?> <?php echo $WCMp_PDF_Invoices->utils->company_name($user_id, 'vendor', $order) ?></strong></p>
                    <p><?php echo $WCMp_PDF_Invoices->utils->invoice_to_address('vendor', $user_id, ', ');?></p><br/>
                    <p class="invoice-text"><strong><?php _e('Invoice to:', 'wcmp-pdf_invoices'); ?> <?php echo $WCMp_PDF_Invoices->utils->company_name($user_id, $user_type, $order); ?></strong></p>
                    <p><?php echo $WCMp_PDF_Invoices->utils->invoice_to_address($user_type, $user_id, ', ',$order);?></p><br/>
                <?php }else{?>
                    <p class="invoice-text"><strong><?php _e('Invoice To:', 'wcmp-pdf_invoices'); ?> <?php echo $WCMp_PDF_Invoices->utils->company_name($user_id, $user_type, $order) ?></strong></p>
                    <p><?php echo $WCMp_PDF_Invoices->utils->invoice_to_address($user_type, $user_id, ', '); ?></p>
                <?php } 
                do_action('after_wcmp_pdf_invoice_to_address_details', $hook_data); ?>
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" class="incvoice-total">
        <tr>
            <td><?php _e('INVOICE TOTAL', 'wcmp-pdf_invoices'); ?></td>
            <td align="right"><?php $item_totals = $WCMp_PDF_Invoices->utils->get_order_item_totals($order->get_id(), $user_id, $user_type, 'total'); echo $item_totals['value']; ?></td>
        </tr>
    </table>
    
    <?php $WCMp_PDF_Invoices->utils->get_order_details_table_as_html($hook_data); ?>

    <div class="thankyou-text">
        <?php $key = 'is_customer_note_'.$user_type; if(isset($settings[$key]) && $settings[$key] == 'Enable' && $order->get_customer_note()) : ?>
            <p><strong style="font-size: 13px; line-height: 16px;"><?php _e('Customer Note', 'wcmp-pdf_invoices'); ?></strong></p>
            <p style="font-size: 12px; line-height: 16px;"><?php echo $order->get_customer_note(); ?></p>
        <?php endif; ?>   
        <?php if($WCMp_PDF_Invoices->utils->get_terms_n_conditions($user_type, $user_id)) : ?>   
            <p><strong style="font-size: 13px; line-height: 16px;"><?php _e('Terms & Conditions', 'wcmp-pdf_invoices'); ?></strong></p>
            <p style="font-size: 12px; line-height: 16px;"><?php echo $WCMp_PDF_Invoices->utils->get_terms_n_conditions($user_type, $user_id); ?></p>
        <?php endif; ?>
    </div>
    <?php do_action( 'after_wcmp_pdf_invoice_template', $hook_data ); ?>  
</div>