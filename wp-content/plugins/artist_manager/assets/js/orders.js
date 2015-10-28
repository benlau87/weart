jQuery(function($) {

    $('.tips').tooltip();
    $('select.grant_access_id').chosen();

    $('ul.order-status').on('click', 'a.waa-edit-status', function(e) {
        $(this).addClass('waa-hide').closest('li').next('li').removeClass('waa-hide');

        return false;
    });

    $('ul.order-status').on('click', 'a.waa-cancel-status', function(e) {
        $(this).closest('li').addClass('waa-hide').prev('li').find('a.waa-edit-status').removeClass('waa-hide');

        return false;
    });

    $('form#waa-order-status-form').on('submit', function(e) {
        e.preventDefault();

        var self = $(this),
            li = self.closest('li');

        li.block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

        $.post( waa.ajaxurl, self.serialize(), function(response) {
            li.unblock();

            var prev_li = li.prev();

            li.addClass('waa-hide');
            prev_li.find('label').replaceWith(response);
            prev_li.find('a.waa-edit-status').removeClass('waa-hide');
        });
    });

    $('form#add-order-note').on( 'submit', function(e) {
        e.preventDefault();

        if (!$('textarea#add-note-content').val()) return;

        $('#waa-order-notes').block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

        $.post( waa.ajaxurl, $(this).serialize(), function(response) {
            $('ul.order_notes').prepend( response );
            $('#waa-order-notes').unblock();
            $('#add-note-content').val('');
        });

        return false;

    })

    $('#waa-order-notes').on( 'click', 'a.delete_note', function() {

        var note = $(this).closest('li.note');

        $('#waa-order-notes').block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'woocommerce_delete_order_note',
            note_id: $(note).attr('rel'),
            security: $('#delete-note-security').val()
        };

        $.post( waa.ajaxurl, data, function(response) {
            $(note).remove();
            $('#waa-order-notes').unblock();
        });

        return false;

    });

    $('.order_download_permissions').on('click', 'button.grant_access', function() {
        var self = $(this),
            product = $('select.grant_access_id').val();

        if (!product) return;

        $('.order_download_permissions').block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'waa_grant_access_to_download',
            product_ids: product,
            loop: $('.order_download_permissions .panel').size(),
            order_id: self.data('order-id'),
            security: self.data('nonce')
        };

        $.post(waa.ajaxurl, data, function( response ) {

            if ( response ) {

                $('#accordion').append( response );

            } else {

                alert('Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.');

            }

            $( '.datepicker' ).datepicker();
            $('.order_download_permissions').unblock();

        });

        return false;
    });

    $('.order_download_permissions').on('click', 'button.revoke_access', function(e){
        e.preventDefault();
        var answer = confirm('Are you sure you want to revoke access to this download?');

        if (answer){

            var self = $(this),
                el = self.closest('.panel');

            var product = self.attr('rel').split(",")[0];
            var file = self.attr('rel').split(",")[1];

            if (product > 0) {

                $(el).block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

                var data = {
                    action: 'woocommerce_revoke_access_to_download',
                    product_id: product,
                    download_id: file,
                    order_id: self.data('order-id'),
                    security: self.data('nonce')
                };

                $.post(waa.ajaxurl, data, function(response) {
                    // Success
                    $(el).fadeOut('300', function(){
                        $(el).remove();
                    });
                });

            } else {
                $(el).fadeOut('300', function(){
                    $(el).remove();
                });
            }

        }

        return false;
    });

});