jQuery(document).ready(function ($) {
    $('.wcmp_create_per_order_pdf_invoice').off().on('click', function (e) {
        e.preventDefault();
        var input = this;
        input.disabled = true;
        var data = {
            action: 'create_per_order_pdf',
            order_id: $(this).attr('data-id'),
        }
        $.post(ajaxurl, data, function (response) {
            window.location = window.location;
        });
    });

    $('.wcmp_create_per_vendor_pdf_invoice').off().on('click', function (e) {
        e.preventDefault();
        var input = this;
        input.disabled = true;
        var data = {
            action: 'create_per_vendor_pdf',
            order_id: $(this).attr('data-id'),
            user_id: $(this).attr('data-user_id')
        }
        $.post(ajaxurl, data, function (response) {
            window.location = window.location;
        });
    });

    $('.wcmp_cancel_per_order_pdf_invoice').off().on('click', function (e) {
        e.preventDefault();
        var input = this;
        input.disabled = true;
        var data = {
            action: 'cancel_per_order_pdf',
            order_id: $(this).attr('data-id')
        }
        $.post(ajaxurl, data, function (response) {
            window.location = window.location;
        });
    });

    $('.wcmp_cancel_per_vendor_pdf_invoice').off().on('click', function (e) {
        e.preventDefault();
        var input = this;
        input.disabled = true;
        var data = {
            action: 'cancel_per_vendor_pdf',
            order_id: $(this).attr('data-id'),
            vendor_name: $(this).attr('data-vendor')
        }
        $.post(ajaxurl, data, function (response) {
            window.location = window.location;
        });
    });
    
    // ******** PDF Invoices ********* //
    
    $('input:radio[name=pdf_invoices_for]').change(function() {
        var order_id_or_vendor_id = $("#post_ID").val();
        var pdf_for = 'order';
        var order_id = order_id_or_vendor_id;
        if($(this).is(':checked')){
        	$('#order_invoice_generate').attr('disabled', true);
        	$('#order_packing_slip_generate').attr('disabled', true);
            order_id_or_vendor_id = $("input[name='pdf_invoices_for']:checked").val();
            pdf_for = $("input[name='pdf_invoices_for']:checked").attr('data-for');
            order_id = $("input[name='pdf_invoices_for']:checked").attr('data-order_id');
        }
        var data = {
            action: 'wcmp_change_order_pdf_generate_url',
            order_id: order_id,
            pdf_for: pdf_for,
            order_id_or_vendor_id: order_id_or_vendor_id
        }
        $.post(ajaxurl, data, function (response) { 
            if(response.invoice_url){
                $('#order_invoice_generate').attr('href', response.invoice_url);
                $('#order_invoice_generate').attr('disabled', false);
            }
            if(response.packing_slip_url){
                $('#order_packing_slip_generate').attr('href', response.packing_slip_url);
                $('#order_packing_slip_generate').attr('disabled', false);
            }
        });
        
    }).trigger('change');
    
    var invoice_no_format = $('#invoice_no_format').parent().parent().parent();
    invoice_no_format.hide();
    if($('#is_invoice_no').is(':checked')) {
        invoice_no_format.show();
    }
    $('#is_invoice_no').change(function() {
        if($(this).is(":checked")) {
            invoice_no_format.show('slow');
        }
        else {
            invoice_no_format.hide('slow');
        }
    });
    
    $('#choose_invoice_template').change(function() {
        var id = $(this).val();
        //$('.pdf_invoices_template_view_wrap #'+id).show().siblings('.pdf_tpl_img').hide();
        var url = $( ".wcmp-preview-pdf-tpl" ).attr('data-href');
        $( ".wcmp-preview-pdf-tpl" ).attr('href', url + '&template=' + id);
    }).trigger('change');
    
    //$('.wcmp_pdf_invoice_data_column input').prop("disabled", true);
    $('.wcmp_pdf_invoice_data_column input').prop("readonly", true);
    $('.wcmp-pdf-invoice-date-number').on('click', function() {
        var id = $(this).attr('data-section_id');
        //$('.wcmp_pdf_invoice_data_column .wcmp-invoice-field-'+ id +' input').prop("disabled", false);
        $('.wcmp_pdf_invoice_data_column .wcmp-invoice-field-'+ id +' input').prop("readonly", false);
    });

});		