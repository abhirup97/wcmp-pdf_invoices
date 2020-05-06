<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/wcmp-pdf-invoices/wcmp_packing_slip_first_template.php
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
<div class="packing_slip1">
    <?php do_action( 'before_wcmp_pdf_invoice_packing_slip_template', $hook_data ); ?>
    <table cellpadding="0" cellspacing="0" class="invoice-detail">
        <tr style="background-color: #d6d6d6;">
            <td width="45%">
                <?php echo $WCMp_PDF_Invoices->utils->header_logo($user_id) ?>
                <?php do_action('before_wcmp_pdf_invoice_packing_slip_soldby_details', $hook_data); ?>
                <p class="invoice-text"><strong><?php _e('Sold by:', 'wcmp-pdf_invoices'); ?><?php echo $WCMp_PDF_Invoices->utils->company_name($user_id); ?></strong></p>
                <p><?php echo $WCMp_PDF_Invoices->utils->invoice_to_address($user_type, $user_id, ', '); ?></p>
                <?php do_action('after_wcmp_pdf_invoice_packing_slip_soldby_details', $hook_data); ?>
            </td>
            <td align="right">
                <h1><?php _e('PACKING SLIP', 'wcmp-pdf_invoices'); ?></h1>
                <h3># <?php echo $order->get_id(); ?></h3>
                <?php do_action('before_wcmp_pdf_invoice_packing_slip_order_details', $hook_data); ?>
                <p><?php _e('Order date:', 'wcmp-pdf_invoices'); ?> <?php echo date_i18n(wc_date_format(), strtotime($order->get_date_created())); ?></p>
                <?php $is_invoice_enable = get_wcmp_pdf_invoices_settings($user_id, 'is_invoice_no'); if($is_invoice_enable == 'Enable' && $invoice_data) : ?>
                <p><?php _e('Invoice no:', 'wcmp-pdf_invoices'); ?> <?php echo $invoice_data['invoice_no']; ?></p>
                <p><?php _e('Invoice date:', 'wcmp-pdf_invoices'); ?> <?php echo date_i18n(wc_date_format(), strtotime($invoice_data['invoice_date']->date)); ?></p>
                <?php endif; ?>
                <p><?php _e('Payment method:', 'wcmp-pdf_invoices'); ?> <?php echo $order->get_payment_method_title(); ?></p>
                <?php do_action('after_wcmp_pdf_invoice_packing_slip_order_details', $hook_data); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?php do_action('before_wcmp_pdf_invoice_packing_slip_customer_details', $hook_data); ?>
                <p class="invoice-text"><strong><?php _e('Address:', 'wcmp-pdf_invoices'); ?></strong></p>
                <p><?php echo $WCMp_PDF_Invoices->utils->invoice_to_address('customer', $order->get_user_id(), '', $order, $pdf_type); ?></p>
                <?php do_action('before_wcmp_pdf_invoice_packing_slip_customer_details', $hook_data); ?>
            </td>
        </tr>
    </table>
    <div class="product-detail-wrap">
        <table class="product-price-table">
            <thead>
                <tr style="background-color: #000;">
                    <?php do_action('before_wcmp_pdf_packing_slip_template_items_table_header', $hook_data); ?>
                    <th width="50%"><?php _e('Product Name', 'wcmp-pdf_invoices'); ?></th>
                    <th><?php _e('Product Quantity', 'wcmp-pdf_invoices') ?></th>
                    <?php do_action('after_wcmp_pdf_packing_slip_template_items_table_header', $hook_data); ?>
                </tr>
            </thead>
            <tbody>
            <?php $items = $WCMp_PDF_Invoices->utils->get_order_items($order->get_id(),$user_id); 
            if( sizeof( $items ) > 0 ) : 
                foreach( $items as $item_id => $item ) : $item_obj = $order->get_item($item_id); ?>
                <tr>
                    <?php do_action('before_wcmp_pdf_packin_slip_template_items_table_data', $hook_data); ?>
                    <td>
                        <?php echo esc_html( $item_obj->get_name() ); ?>
                        <ul class="meta">
                        <?php $show_order_itemmeta = $WCMp_PDF_Invoices->utils->show_order_itemmeta();
                        if ( $meta_data = $item_obj->get_formatted_meta_data( '' ) ) :
                            foreach ( $meta_data as $meta_id => $meta ) :
				if ( !in_array( $meta->key, $show_order_itemmeta, true ) ) {
					continue;
				}
                                if($meta->key == '_vendor_id'){
                                    $meta->display_key = __('Sold by', 'wcmp-pdf_invoices');
                                    $vendor = get_wcmp_vendor($meta->value);
                                    $meta->display_value = $vendor->page_title;
                                }
				?>
                            <li class="item-meta"><span><?php echo wp_kses_post( $meta->display_key ); ?>:</span> <span><?php echo wp_kses_post( force_balance_tags( $meta->display_value ) ); ?></span></li>
                        <?php endforeach; endif; ?>
                        </ul>
                    </td>
                    <td><?php echo esc_html( $item_obj->get_quantity() ); ?></td>
                    <?php do_action('after_wcmp_pdf_packin_slip_template_items_table_data', $hook_data); ?>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($order->get_customer_note() && apply_filters('wcmp_pdf_invoice_packing_slip_show_customer_note', true)) : ?>
    <div class="thankyou-text">
        <p><strong style="font-size: 13px; line-height: 16px;"><?php _e('Customer Note', 'wcmp-pdf_invoices'); ?></strong></p>
        <p style="font-size: 12px; line-height: 16px;"><?php echo $order->get_customer_note(); ?></p>
    </div>
    <?php endif; ?>
    <?php do_action( 'after_wcmp_pdf_invoice_packing_slip_template', $hook_data ); ?>
</div>