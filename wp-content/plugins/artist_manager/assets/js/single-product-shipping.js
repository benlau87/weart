
// For single page shipping calculation scripts;
(function($){

    $(document).ready(function(){
        $( '.waa-shipping-calculate-wrapper' ).on( 'change', 'select#waa-shipping-country', function(e) {
            e.preventDefault();
            
            var self = $(this),
                data = {
                    'action' : 'waa_shipping_country_select',
                    'country_id' : self.val(),
                    'author_id' : self.data('author_id'),
                };

            if( self.val() != '' ) {
                $.post( waa.ajaxurl, data, function( resp ) {
                    
                    if( resp.success ) {
                        self.closest('.waa-shipping-calculate-wrapper').find('.waa-shipping-state-wrapper').html( resp.data );
                        self.closest('.waa-shipping-calculate-wrapper').find('.waa-shipping-price-wrapper').html('');
                    }
                });
            } else {
                self.closest('.waa-shipping-calculate-wrapper').find('.waa-shipping-price-wrapper').html('');
                self.closest('.waa-shipping-calculate-wrapper').find('.waa-shipping-state-wrapper').html('');
            }   
        });

        $('.waa-shipping-calculate-wrapper').on( 'keydown', '#waa-shipping-qty', function(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 107, 109, 110, 187, 189, 190]) !== -1 ||
                 // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                 // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                     // let it happen, don't do anything
                    return;
            }

            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        $( '.waa-shipping-calculate-wrapper' ).on( 'click', 'button.waa-shipping-calculator', function(e) {
            e.preventDefault();
            
            var self = $(this),
                data = {
                    'action' : 'waa_shipping_calculator',
                    'country_id' : self.closest('.waa-shipping-calculate-wrapper').find('select.waa-shipping-country').val(),
                    'product_id' : self.closest('.waa-shipping-calculate-wrapper').find('select.waa-shipping-country').data('product_id'),
                    'author_id' : self.closest('.waa-shipping-calculate-wrapper').find('select.waa-shipping-country').data('author_id'),
                    'quantity' : self.closest('.waa-shipping-calculate-wrapper').find('input.waa-shipping-qty').val(),
                    'state' : self.closest('.waa-shipping-calculate-wrapper').find('select.waa-shipping-state').val(),
                };
                
            $.post( waa.ajaxurl, data, function( resp ) {
                if( resp.success ) {
                    self.closest('.waa-shipping-calculate-wrapper').find('.waa-shipping-price-wrapper').html( resp.data );
                }
            });  
        });
    });

})(jQuery);