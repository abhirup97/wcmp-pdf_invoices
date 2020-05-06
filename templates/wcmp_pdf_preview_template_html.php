<?php global $WCMp_PDF_Invoices; ?>

<?php if($template == 'wcmp_pdf_invoice_first_template') { ?>
<!-- invoice1 start -->

<div class="preview-invoice invoice1">
    <table cellpadding="0" cellspacing="0" class="invoice-detail">
        <tr style="background-color: #252525;">
            <td width="45%">
                <img src="<?php echo $WCMp_PDF_Invoices->plugin_url . 'assets/images/logo.png' ?>" alt="logo" width="120" class="logo">
            </td>
            <td align="right">
                <h2>INVOICE</h2>
            </td>
        </tr> 
        <tr style="background-color: #d6d6d6;">
            <td style="padding-top:25px; padding-bottom:25px;">
                <p class="invoice-text"><strong>Invoice To: ABC Infotech</strong></p>
                <p>BB-164, BB Block, Sector 1, Salt Lake City, Kolkata, West Bengal 700064</p>
            </td>
            <td align="right" style="padding-top:25px; padding-bottom:25px;" class="invoice-info">
                <table align="right">
                    <tbody>
                        <tr>
                            <td>Invoice no:</td>
                            <td>201815311526476267</td>
                        </tr>
                        <tr>
                            <td>Invoice date:</td>
                            <td>May 16, 2018</td>
                        </tr>
                        <tr>
                            <td>Order Number:</td>
                            <td>2015</td>
                        </tr>
                        <tr>
                            <td>Order Date:</td>
                            <td>01/03/2018</td>
                        </tr>
                        <tr>
                            <td>Payment Method:</td>
                            <td>Cash on Delivery</td>
                        </tr>
                    </tbody>
                </table>
           </td>
        </tr>
    </table>
    <div class="product-detail-wrap">
        <table class="product-price-table">
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th align="center">Quantity</th>
                    <th align="right" style="padding-left: 55px;">Price</th>
                    <th align="right" style="padding-left: 25px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   <td>Beanie with Logo
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$250.00</td>
                   <td align="right">$200.00</td>
                </tr>
                <tr>
                   <td>New Dress
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$500.00</td>
                   <td align="right">$450.00</td>
                </tr>
               <tr>
                   <td>Ninja Shirt
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$550.00</td>
                   <td align="right">$550.00</td>
               </tr>
            </tbody>
            <tfoot align="right">
                <tr>
                   <td colspan="2"></td>
                   <td>Subtotal:</td>
                   <td>$1200.00</td>
               </tr>
               <tr>
                   <td colspan="2"></td>
                   <td>Tax (10%):</td>
                   <td>$135.00</td>
               </tr>
               <tr>
                   <td colspan="2"></td>
                   <td>Total:</td>
                   <td>$1335.00</td>
               </tr>
               <tr>
                   <td colspan="2"></td>
                   <td>Amount Paid:</td>
                   <td>$997.00</td>
               </tr>
            </tfoot>
        </table>
    </div>
    <div class="terms-text">
        <p><strong style="font-size: 13px; line-height: 16px;">Terms & Conditions</strong></p>
        <p style="font-size: 12px; line-height: 16px;">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p>
    </div>
</div>

<!-- invoice1 start -->

<?php } elseif($template == 'wcmp_pdf_invoice_second_template') { ?>

<!-- invoice2 start -->
 
<div class="preview-invoice invoice2">
    <table cellpadding="0" cellspacing="0" class="invoice-detail">
        <tr>
            <td width="45%">
                <img src="<?php echo $WCMp_PDF_Invoices->plugin_url . 'assets/images/logo.png' ?>" alt="logo" width="120" class="logo">
                <p class="invoice-text"><strong>Invoice To: ABC Infotech</strong></p>
                <p>BB-164, BB Block, Sector 1, Salt Lake City, Kolkata, West Bengal 700064</p>
            </td>
            <td align="right">
                <h1>INVOICE</h1>
                <h3>DC51526643424</h3>
                <p>Invoice Date: <label>01/03/2018</label></p>
                <p>Order Number: <label>2015</label></p>
                <p>Order Date: <label>01/03/2018</label></p>
                <p>Payment Method : <label>Cash on Delivery</label></p>
            </td>
        </tr> 
    </table>
    <div class="product-detail-wrap">
        <table class="product-price-table">
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th>Quantity</th>
                    <th align="right" style="padding-left: 55px;">Price</th>
                    <th align="right" style="padding-left: 25px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   <td>Beanie with Logo
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$250.00</td>
                   <td align="right">$200.00</td>
                </tr>
                <tr>
                   <td>New Dress
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$500.00</td>
                   <td align="right">$450.00</td>
                </tr>
                <tr>
                   <td>Ninja Shirt
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$550.00</td>
                   <td align="right">$550.00</td>
                </tr>
            </tbody>
            <tfoot align="right">
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal:</td>
                    <td>$1200.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Tax (10%):</td>
                    <td>$135.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Total:</td>
                    <td>$1335.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Amount Paid:</td>
                    <td>$997.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="terms-text">
        <p><strong style="font-size: 13px; line-height: 16px;">Terms & Conditions</strong></p>
        <p style="font-size: 12px; line-height: 16px;">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p>
    </div>
</div>

<!-- invoice2 start -->

<?php } elseif($template == 'wcmp_pdf_invoice_third_template') { ?>

<!-- invoice3 start -->

<div class="preview-invoice invoice4 invoice4-red">
    <table cellpadding="0" cellspacing="0" class="invoice-detail">
        <tr>
            <td width="50%">
                <h1>INVOICE</h1>
                <h3>DC51526643424</h3>
                <p><label>Invoice Date:</label> 01/03/2018</p>
                <p><label>Order Number:</label> 2015</p>
                <p><label>Order Date:</label> 01/03/2018</p>
                <p><label>Payment Method:</label> <strong> Cash on Delivery</strong></p>
            </td>
            <td>
                <img src="<?php echo $WCMp_PDF_Invoices->plugin_url . 'assets/images/logo.png' ?>" alt="logo" width="120" class="logo">
                <p class="invoice-text"><strong>Invoice To: ABC Infotech</strong></p>
                <p>BB-164, BB Block, Sector 1, Salt Lake City, Kolkata, West Bengal 700064</p>
            </td>
        </tr> 
    </table>
    <div class="thankyou-text">
        <p><strong style="font-size: 13px; line-height: 16px;">Customer's Note</strong></p>
        <p style="font-size: 12px; line-height: 16px;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
    </div>
    <div class="product-detail-wrap">
        <table class="product-price-table">
            <thead>
                <tr>
                    <th width="55%">Item</th>
                    <th>Quantity</th>
                    <th align="right" style="padding-left: 55px;">Price</th>
                    <th align="right" style="padding-left: 25px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   <td>Beanie with Logo
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$250.00</td>
                   <td align="right">$200.00</td>
                </tr>
                <tr>
                   <td>New Dress
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$500.00</td>
                   <td align="right">$450.00</td>
                </tr>
               <tr>
                   <td>Ninja Shirt
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$550.00</td>
                   <td align="right">$550.00</td>
               </tr>
            </tbody>
            <tfoot align="right">
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal:</td>
                    <td>$1200.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Tax (10%):</td>
                    <td>$135.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Total:</td>
                    <td>$1335.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Amount Paid:</td>
                    <td>$997.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="terms-text">
        <p><strong style="font-size: 13px; line-height: 16px;">Terms and Conditions:</strong></p>
        <p style="font-size: 12px; line-height: 16px;">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.</p>
    </div>
</div>

<!-- invoice3 start -->

<?php } elseif($template == 'wcmp_pdf_invoice_forth_template') { ?>

<!-- invoice4 start -->
 
<div class="preview-invoice invoice4">
    <table cellpadding="0" cellspacing="0" class="invoice-detail">
        <tr>
            <td width="50%">
                <h1>INVOICE</h1>
                <h3>DC51526643424</h3>
                <p><label>Invoice Date:</label> 01/03/2018</p>
                <p><label>Order Number:</label> 2015</p>
                <p><label>Order Date:</label> 01/03/2018</p>
                <p><label>Payment Method:</label> <strong>Cash on Delivery</strong></p>
            </td>
            <td>
                <img src="<?php echo $WCMp_PDF_Invoices->plugin_url . 'assets/images/logo.png' ?>" alt="logo" width="120" class="logo">
                <p class="invoice-text"><strong>Invoice To: ABC Infotech</strong></p>
                <p>BB-164, BB Block, Sector 1, Salt Lake City, Kolkata, West Bengal 700064</p>
            </td>
        </tr> 
    </table>
    <div class="thankyou-text">
        <p><strong style="font-size: 13px; line-height: 16px;">Customer's Note</strong></p>
        <p style="font-size: 12px; line-height: 16px;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
    </div>
    <div class="product-detail-wrap">
        <table class="product-price-table">
            <thead>
                <tr>
                    <th width="55%">Item</th>
                    <th>Quantity</th>
                    <th align="right" style="padding-left: 55px;">Price</th>
                    <th align="right" style="padding-left: 25px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   <td>Beanie with Logo
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$250.00</td>
                   <td align="right">$200.00</td>
                </tr>
                <tr>
                   <td>New Dress
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$500.00</td>
                   <td align="right">$450.00</td>
                </tr>
               <tr>
                   <td>Ninja Shirt
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$550.00</td>
                   <td align="right">$550.00</td>
               </tr>
            </tbody>
            <tfoot align="right">
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal:</td>
                    <td>$1200.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Tax (10%):</td>
                    <td>$135.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Total:</td>
                    <td>$1335.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Amount Paid:</td>
                    <td>$997.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="terms-text">
        <p><strong style="font-size: 13px; line-height: 16px;">Terms and Conditions:</strong></p>
        <p style="font-size: 12px; line-height: 16px;">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.</p>
    </div>
</div>

<!-- invoice4 End -->

<?php } elseif($template == 'wcmp_pdf_invoice_fifth_template') { ?>

<!-- invoice5 start -->
 
<div class="preview-invoice invoice5">
    <table cellpadding="0" cellspacing="0" width="100%" class="invoice-detail">
        <tr>
            <td>
                <img src="<?php echo $WCMp_PDF_Invoices->plugin_url . 'assets/images/logo.png' ?>" alt="logo" width="120" class="logo">
                <table align="right">
                    <tbody>
                        <tr>
                            <td>Invoice no:</td>
                            <td>201815311526476267</td>
                        </tr>
                        <tr>
                            <td>Invoice date:</td>
                            <td>May 16, 2018</td>
                        </tr>
                        <tr>
                            <td>Order Number:</td>
                            <td>2015</td>
                        </tr>
                        <tr>
                            <td>Order Date:</td>
                            <td>01/03/2018</td>
                        </tr>
                        <tr>
                            <td>Payment Method:</td>
                            <td>Cash on Delivery</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="42%" style=" padding-top: 40px; ">
                <p class="invoice-text"><strong>Invoice To: ABC Infotech</strong></p>
                <p>BB-164, BB Block, Sector 1, Salt Lake City, Kolkata, West Bengal 700064</p>
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" class="incvoice-total">
        <tr>
            <td>INVOICE TOTAL</td>
            <td align="right">$1350.00</td>
        </tr>
    </table>
    <div class="product-detail-wrap">
        <table class="product-price-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th>Quantity</th>
                    <th align="right" style="padding-left: 55px;">Price</th>
                    <th align="right" style="padding-left: 25px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   <td>Beanie with Logo
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$250.00</td>
                   <td align="right">$200.00</td>
                </tr>
                <tr>
                   <td>New Dress
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$500.00</td>
                   <td align="right">$450.00</td>
                </tr>
                <tr>
                   <td>Ninja Shirt
                   <ul><li>Sold By: Test Shop</li></ul></td>
                   <td align="center">1</td>
                   <td align="right">$550.00</td>
                   <td align="right">$550.00</td>
                </tr>
            </tbody>
            <tfoot align="right">
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal:</td>
                    <td>$1200.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Tax (10%):</td>
                    <td>$135.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Total:</td>
                    <td>$1335.00</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Amount Paid:</td>
                    <td>$997.00</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="thankyou-text">
        <p><strong style="font-size: 12px; line-height: 16px;">Terms and Conditions:</strong></p>
        <p style="font-size: 12px; line-height: 16px;">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p>
    </div>
</div> 

<!-- invoice5 End -->
<?php } else{ do_action('wcmp_pdf_invoice_preview_template_htmls', $template); } ?>