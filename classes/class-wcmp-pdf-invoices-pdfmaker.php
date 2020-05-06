<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WCMp_PDF_Invoices_PDF_Maker')) :

    class WCMp_PDF_Invoices_PDF_Maker {

        public $html;
        public $settings;

        public function __construct($html, $settings = array()) {
            $this->html = $html;

            $default_settings = array(
                'paper_size' => 'A4',
                'paper_orientation' => 'portrait',
                'font_subsetting' => false,
            );
            $this->settings = $settings + $default_settings;
        }

        public function output() {
            global $WCMp_PDF_Invoices;
            if (empty($this->html)) {
                return;
            }
            
            require $WCMp_PDF_Invoices->plugin_path . 'lib/autoload.php';

            // set options
            $options = new Options(apply_filters('wcmp_pdf_invoices_dompdf_options', array(
                        'defaultFont'           => 'dejavu sans',
                        'isRemoteEnabled'       => true,
                        'fontDir'		=> $WCMp_PDF_Invoices->utils->get_wcmp_pdf_tmp_path('fonts'),
			'fontCache'		=> $WCMp_PDF_Invoices->utils->get_wcmp_pdf_tmp_path('fonts'),
                        'font_subsetting'       => false,
                        'isHtml5ParserEnabled'	=> ( extension_loaded('iconv') ) ? true : false,
                    )));

            // instantiate and use the dompdf class
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($this->html);
            $dompdf->setPaper($this->settings['paper_size'], $this->settings['paper_orientation']);
            $dompdf = apply_filters('wcmp_pdf_invoices_before_dompdf_render', $dompdf, $this->html);
            $dompdf->render();
            $dompdf = apply_filters('wcmp_pdf_invoices_after_dompdf_render', $dompdf, $this->html);
            do_action('wcmp_pdf_invoices_before_dompdf_output', $dompdf, $this->html);
            return $dompdf->output();
        }

    }

endif; // class_exists
