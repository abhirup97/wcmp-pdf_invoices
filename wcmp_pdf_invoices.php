<?php

/**
 * Plugin Name: WCMp PDF Invoices
 * Plugin URI: https://wc-marketplace.com
 * Description: A dynamic add-on for admin to automatically create and send PDF invoice to vendors.
 * Version: 2.0.7
 * Author: WC Marketplace
 * Author URI: https://wc-marketplace.com
 * WC requires at least: 3.0
 * WC tested up to: 3.8.0
 * Requires at least: 4.2
 * Tested up to: 5.2.4
 * Text Domain: wcmp-pdf_invoices
 * Domain Path: /languages/
 */
if (!class_exists('WCMp_Dependencies_PDF_Invoices')) {
    require_once 'includes/class-wcmp-pdf-invoices-dependencies.php';
}
require_once 'includes/wcmp-pdf-invoices-core-functions.php';
require_once 'wcmp_pdf_invoices_config.php';

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!defined('WCMp_PDF_INVOICES_PLUGIN_TOKEN'))
    exit;
if (!defined('WCMp_PDF_INVOICES_TEXT_DOMAIN'))
    exit;

if (!WCMp_Dependencies_PDF_Invoices::woocommerce_plugin_active_check()) {
    add_action('admin_notices', 'woocommerce_inactive_notice');
}

if (!WCMp_Dependencies_PDF_Invoices::wc_marketplace_plugin_active_check()) {
    add_action('admin_notices', 'wcmp_inactive_notice');
}

if (!class_exists('WCMp_PDF_Invoices') && WCMp_Dependencies_PDF_Invoices::woocommerce_plugin_active_check() && WCMp_Dependencies_PDF_Invoices::wc_marketplace_plugin_active_check()) {
    require_once( 'classes/class-wcmp-pdf-invoices.php' );
    global $WCMp_PDF_Invoices;
    $WCMp_PDF_Invoices = new WCMp_PDF_Invoices(__FILE__);
    $GLOBALS['WCMp_PDF_Invoices'] = $WCMp_PDF_Invoices;
}
