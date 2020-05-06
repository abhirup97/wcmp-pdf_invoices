<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/wcmp-pdf-invoices/wcmp_pdf_invoice_forth_template.php
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
<div class="invoice4">
    <?php do_action( 'before_wcmp_pdf_invoice_template', $hook_data ); ?>
    <table cellpadding="0" cellspacing="0" class="invoice-detail">
        <tr>
            <td width="50%">
                <?php do_action('before_wcmp_pdf_invoice_order_invoice_details', $hook_data); ?>
                <h1><?php _e('INVOICE', 'wcmp-pdf_invoices'); ?></h1>
                <?php $is_invoice_enable = get_wcmp_pdf_invoices_settings($user_id, 'is_invoice_no'); if($is_invoice_enable == 'Enable' && $invoice_data) : ?>
                <h3># <?php echo $invoice_data['invoice_no']; ?></h3>
                <p><label><?php _e('Invoice date:', 'wcmp-pdf_invoices'); ?></label> <?php echo date_i18n(wc_date_format(), strtotime($invoice_data['invoice_date']->date)); ?></p>
                <?php endif; ?>
                <p><label><?php _e('Order no:', 'wcmp-pdf_invoices'); ?></label> <?php echo $order->get_id(); ?></p>
                <p><label><?php _e('Order date:', 'wcmp-pdf_invoices'); ?></label> <?php echo date_i18n(wc_date_format(), strtotime($order->get_date_created())); ?></p>
                <?php $key = 'is_payment_method_'.$user_type; if(isset($settings[$key]) && $settings[$key] == 'Enable') : ?>
                <p><label><?php _e('Payment method:', 'wcmp-pdf_invoices'); ?></label> <?php echo $order->get_payment_method_title(); ?></p>
                <?php endif; ?>
                <?php do_action('after_wcmp_pdf_invoice_order_invoice_details', $hook_data); ?>
            </td>
            <td>
                <?php echo $WCMp_PDF_Invoices->utils->header_logo($user_id) ?>
                <?php do_action('before_wcmp_pdf_invoice_to_address_details', $hook_data); 
                if($user_type == 'customer'){
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
    <?php $key = 'is_customer_note_'.$user_type; if(isset($settings[$key]) && $settings[$key] == 'Enable' && $order->get_customer_note()) : ?>
    <div class="thankyou-text">
        <p><strong style="font-size: 13px; line-height: 16px;"><?php _e('Customer Note', 'wcmp-pdf_invoices'); ?></strong></p>
        <p style="font-size: 12px; line-height: 16px;"><?php echo $order->get_customer_note(); ?></p>
    </div>
    <?php endif; ?>
    
    <?php $WCMp_PDF_Invoices->utils->get_order_details_table_as_html($hook_data); ?>
    
    <?php if($WCMp_PDF_Invoices->utils->get_terms_n_conditions($user_type, $user_id)) : ?>   
    <div class="terms-text">
        <p><strong style="font-size: 13px; line-height: 16px;"><?php _e('Terms & Conditions', 'wcmp-pdf_invoices'); ?></strong></p>
        <p style="font-size: 12px; line-height: 16px;"><?php echo $WCMp_PDF_Invoices->utils->get_terms_n_conditions($user_type, $user_id); ?></p>
    </div>
    <?php endif; ?>
    <?php do_action( 'after_wcmp_pdf_invoice_template', $hook_data ); ?>  
</div>