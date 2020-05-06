<?php global $WCMp_PDF_Invoices; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $WCMp_PDF_Invoices->utils->get_filename($pdf_type, $pdf_args['order_ids'], $pdf_args['user_id'], $pdf_args); ?></title>
        <style type="text/css"><?php $WCMp_PDF_Invoices->utils->template_styles(); ?></style>
        <style type="text/css"><?php do_action('wcmp_pdf_invoices_pdf_html_custom_styles', $pdf_type, $this); ?></style>
    </head>
    <body class="wcmp-pdf-html-body-<?php echo $pdf_type; ?> <?php echo $template; ?>">
        <div id="footer"><p class="page"><?php echo $WCMp_PDF_Invoices->utils->get_footer_content($pdf_type, $pdf_args);?></p></div>
        <?php echo $content; ?>
    </body>
</html>