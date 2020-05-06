jQuery(document).ready(function($) {		
    $( ".vendor_preferred_template" ).on('change', function() {
        //var url = $('option:selected', this).attr('data-tpl_img');
        //$('#wcmp-invoice-tpl-modal-image').attr('src', url);
        var url = $( ".wcmp-preview-pdf-tpl" ).attr('data-href');
        $( ".wcmp-preview-pdf-tpl" ).attr('href', url + '&template=' + $( ".vendor_preferred_template" ).val());
    }).trigger('change');
    
//    $( ".wcmp-preview-pdf-tpl" ).on('click', function(e) {
//        e.preventDefault();
//        var url = $(this).attr('data-href');
//        console.log(url + '&template=' + $( ".vendor_preferred_template" ).val());
//    }).trigger('change');   
});