jQuery(function($) {

    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('.tips').tooltip();

    // set dashboard menu height
    var dashboardMenu = $('ul.waa-dashboard-menu'),
        contentArea = $('.waa-dashboard-content');

    if ( $(window).width() > 767) {
        if ( contentArea.height() > dashboardMenu.height() ) {
            dashboardMenu.css({ height: contentArea.height() });
        }
    }

    function showTooltip(x, y, contents) {
        jQuery('<div class="chart-tooltip">' + contents + '</div>').css({
            top: y - 16,
            left: x + 20
        }).appendTo("body").fadeIn(200);
    }

    var prev_data_index = null;
    var prev_series_index = null;

    jQuery(".chart-placeholder").bind("plothover", function(event, pos, item) {
        if (item) {
            if (prev_data_index != item.dataIndex || prev_series_index != item.seriesIndex) {
                prev_data_index = item.dataIndex;
                prev_series_index = item.seriesIndex;

                jQuery(".chart-tooltip").remove();

                if (item.series.points.show || item.series.enable_tooltip) {

                    var y = item.series.data[item.dataIndex][1];

                    tooltip_content = '';

                    if (item.series.prepend_label)
                        tooltip_content = tooltip_content + item.series.label + ": ";

                    if (item.series.prepend_tooltip)
                        tooltip_content = tooltip_content + item.series.prepend_tooltip;

                    tooltip_content = tooltip_content + y;

                    if (item.series.append_tooltip)
                        tooltip_content = tooltip_content + item.series.append_tooltip;

                    if (item.series.pie.show) {

                        showTooltip(pos.pageX, pos.pageY, tooltip_content);

                    } else {

                        showTooltip(item.pageX, item.pageY, tooltip_content);

                    }

                }
            }
        } else {
            jQuery(".chart-tooltip").remove();
            prev_data_index = null;
        }
    });

});

// waa Register

jQuery(function($) {
    $('.user-role input[type=radio]').on('change', function() {
        var value = $(this).val();

        if ( value === 'seller') {
            $('.show_if_seller').slideDown();
        } else {
            $('.show_if_seller').slideUp();
        }
    });

    $('#company-name').on('focusout', function() {
        var value = $(this).val().toLowerCase().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        $('#seller-url').val(value);
        $('#url-alart').text( value );
        $('#seller-url').focus();
    });

    $('#seller-url').keydown(function(e) {
        var text = $(this).val();

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 109, 110, 173, 189, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                return;
        }

        if ((e.shiftKey || (e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) ) {
            e.preventDefault();
        }
    });

    $('#seller-url').keyup(function(e) {
        $('#url-alart').text( $(this).val() );
    });

    $('#shop-phone').keydown(function(e) {
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

    $('#seller-url').on('focusout', function() {
        var self = $(this),
        data = {
            action : 'shop_url',
            url_slug : self.val(),
            _nonce : waa.nonce,
        };

        if ( self.val() === '' ) {
            return;
        }

        var row = self.closest('.form-row');
        row.block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

        $.post( waa.ajaxurl, data, function(resp) {

            if ( resp == 0){
                $('#url-alart').removeClass('text-success').addClass('text-danger');
                $('#url-alart-mgs').removeClass('text-success').addClass('text-danger').text(waa.seller.notAvailable);
            } else {
                $('#url-alart').removeClass('text-danger').addClass('text-success');
                $('#url-alart-mgs').removeClass('text-danger').addClass('text-success').text(waa.seller.available);
            }

            row.unblock();

        } );

    });
});

//waa settings

(function($) {

    $.validator.setDefaults({ ignore: ":hidden" });

    var validatorError = function(error, element) {
        var form_group = $(element).closest('.form-group');
        form_group.addClass('has-error').append(error);
    };

    var validatorSuccess = function(label, element) {
        $(element).closest('.form-group').removeClass('has-error');
    };

    var waa_Settings = {
        init: function() {
            var self = this;

            //image upload
            $('a.waa-banner-drag').on('click', this.imageUpload);
            $('a.waa-remove-banner-image').on('click', this.removeBanner);

            $('a.waa-gravatar-drag').on('click', this.gragatarImageUpload);
            $('a.waa-remove-gravatar-image').on('click', this.removeGravatar);

            this.validateForm(self);

            return false;
        },


        imageUpload: function(e) {
            e.preventDefault();

            var file_frame,
                self = $(this);

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery( this ).data( 'uploader_title' ),
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' )
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                var attachment = file_frame.state().get('selection').first().toJSON();

                var wrap = self.closest('.waa-banner');
                wrap.find('input.waa-file-field').val(attachment.id);
                wrap.find('img.waa-banner-img').attr('src', attachment.url);
                self.parent().siblings('.image-wrap', wrap).removeClass('waa-hide');

                self.parent('.button-area').addClass('waa-hide');
            });

            // Finally, open the modal
            file_frame.open();

        },
        gragatarImageUpload: function(e) {
            e.preventDefault();

            var file_frame,
                self = $(this);

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery( this ).data( 'uploader_title' ),
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' )
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                var attachment = file_frame.state().get('selection').first().toJSON();

                var wrap = self.closest('.waa-gravatar');
                wrap.find('input.waa-file-field').val(attachment.id);
                wrap.find('img.waa-gravatar-img').attr('src', attachment.url);
                self.parent().siblings('.gravatar-wrap', wrap).removeClass('waa-hide');
                self.parent('.gravatar-button-area').addClass('waa-hide');

            });

            // Finally, open the modal
            file_frame.open();

        },

        submitSettings: function(form_id) {

            if ( typeof tinyMCE != 'undefined' ) {
                tinyMCE.triggerSave();
            }

            var self = $( "form#" + form_id ),
                form_data = self.serialize() + '&action=waa_settings&form_id=' + form_id;

            self.find('.ajax_prev').append('<span class="waa-loading"> </span>');
            $.post(waa.ajaxurl, form_data, function(resp) {

                self.find('span.waa-loading').remove();
                $('html,body').animate({scrollTop:100});

               if ( resp.success ) {
                    // Harcoded Customization for template-settings function
                      $('.waa-ajax-response').html( $('<div/>', {
                        'class': 'waa-alert waa-alert-success',
                        'html': '<p>' + resp.data.msg + '</p>',
                    }) );

                    $('.waa-ajax-response').append(resp.data.progress);

                }else {
                    $('.waa-ajax-response').html( $('<div/>', {
                        'class': 'waa-alert waa-alert-danger',
                        'html': '<p>' + resp.data + '</p>'
                    }) );
                }
            });
        },

        validateForm: function(self) {

            $("form#settings-form, form#profile-form, form#store-form, form#payment-form").validate({
                //errorLabelContainer: '#errors'
                submitHandler: function(form) {
                    self.submitSettings( form.getAttribute('id') );
                },
                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            });

        },

        removeBanner: function(e) {
            e.preventDefault();

            var self = $(this);
            var wrap = self.closest('.image-wrap');
            var instruction = wrap.siblings('.button-area');

            wrap.find('input.waa-file-field').val('0');
            wrap.addClass('waa-hide');
            instruction.removeClass('waa-hide');
        },

        removeGravatar: function(e) {
            e.preventDefault();

            var self = $(this);
            var wrap = self.closest('.gravatar-wrap');
            var instruction = wrap.siblings('.gravatar-button-area');

            wrap.find('input.waa-file-field').val('0');
            wrap.addClass('waa-hide');
            instruction.removeClass('waa-hide');
        },
    };

    var waa_Withdraw = {

        init: function() {
            var self = this;

            this.withdrawValidate(self);
        },

        withdrawValidate: function(self) {
            $('form.withdraw').validate({
                //errorLabelContainer: '#errors'

                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            })
        }
    };

    var waa_Coupons = {
        init: function() {
            var self = this;
            this.couponsValidation(self);
        },

        couponsValidation: function(self) {
            $("form.coupons").validate({
                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            });
        }
    };

    var waa_Seller = {
        init: function() {
            this.validate(this);
        },

        validate: function(self) {
            // e.preventDefault();

            $('form#waa-form-contact-seller').validate({
                errorPlacement: validatorError,
                success: validatorSuccess,
                submitHandler: function(form) {

                    $(form).block({ message: null, overlayCSS: { background: '#fff url(' + waa.ajax_loader + ') no-repeat center', opacity: 0.6 } });

                    var form_data = $(form).serialize();
                    $.post(waa.ajaxurl, form_data, function(resp) {
                        $(form).unblock();

                        if ( typeof resp.data !== 'undefined' ) {
                            $(form).find('.ajax-response').html(resp.data);
                        }

                        $(form).find('input[type=text], input[type=email], textarea').val('').removeClass('valid');
                    });
                }
            });
        }
    };

    var waa_Add_Seller = {
        init: function() {
            this.validate(this);
        },

        validate: function(self) {

            $('form.register').validate({
                errorPlacement: validatorError,
                success: validatorSuccess,
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    };

    $(function() {
        waa_Settings.init();
        waa_Withdraw.init();
        waa_Coupons.init();
        waa_Seller.init();
        waa_Add_Seller.init();
    });

})(jQuery);

// Shipping tab js
(function($){
    $(document).ready(function(){

        $('.waa-shipping-location-wrapper').on('change', '.dps_country_selection', function() {
            var self = $(this),
                data = {
                    country_id : self.find(':selected').val(),
                    action  : 'dps_select_state_by_country'
                };

                if ( self.val() == '' || self.val() == 'everywhere' ) {
                    self.closest('.dps-shipping-location-content').find('table.dps-shipping-states tbody').html('');
                } else {
                    $.post( waa.ajaxurl, data, function(response) {
                        if( response.success ) {
                            self.closest('.dps-shipping-location-content').find('table.dps-shipping-states tbody').html(response.data);
                        }
                    });
                }
        });

    });
})(jQuery);


(function($){

    $(document).ready(function(){

        $('.dps-main-wrapper').on('click', 'a.dps-shipping-add', function(e) {
            e.preventDefault();

            html = $('#dps-shipping-hidden-lcoation-content');
            var row = $(html).first().clone().appendTo($('.waa-shipping-location-wrapper')).show();
            $('.waa-shipping-location-wrapper').find('.dps-shipping-location-content').first().find('a.dps-shipping-remove').show();

            $('.tips').tooltip();

            row.removeAttr('id');
            row.find('input,select').val('');
            row.find('a.dps-shipping-remove').show();
        });

        $('.waa-shipping-location-wrapper').on('click', 'a.dps-shipping-remove', function(e) {
            e.preventDefault();
            $(this).closest('.dps-shipping-location-content').remove();
            $dpsElm = $('.waa-shipping-location-wrapper').find('.dps-shipping-location-content');

            if( $dpsElm.length == 1) {
                $dpsElm.first().find('a.dps-shipping-remove').hide();
            }
        });

        $('.waa-shipping-location-wrapper').on('click', 'a.dps-add', function(e) {
            e.preventDefault();

            var row = $(this).closest('tr').first().clone().appendTo($(this).closest('table.dps-shipping-states'));
            row.find('input,select').val('');
            row.find('a.dps-remove').show();
            $('.tips').tooltip();
        });

        $('.waa-shipping-location-wrapper').on('click', 'a.dps-remove', function(e) {
            e.preventDefault();

            if( $(this).closest('table.dps-shipping-states').find( 'tr' ).length == 1 ){
                console.log($(this).closest('.dps-shipping-location-content').find('input,select'));
                $(this).closest('.dps-shipping-location-content').find('td.dps_shipping_location_cost').show();
            }

            $(this).closest('tr').remove();


        });

        $('.waa-shipping-location-wrapper').on('change keyup', '.dps_state_selection', function() {
            var self = $(this);

            if( self.val() == '' || self.val() == '-1' ) {
                self.closest('.dps-shipping-location-content').find('td.dps_shipping_location_cost').show();
            } else {
                self.closest('.dps-shipping-location-content').find('td.dps_shipping_location_cost').hide();
            }
        });

        $('.waa-shipping-location-wrapper .dps_state_selection').trigger('change');
        $('.waa-shipping-location-wrapper .dps_state_selection').trigger('keyup');

        $wrap = $('.waa-shipping-location-wrapper').find('.dps-shipping-location-content');

        if( $wrap.length == 1) {
            $wrap.first().find('a.dps-shipping-remove').hide();
        }

    });

})(jQuery);

// For Announcement scripts;
(function($){

    $(document).ready(function(){
        $( '.waa-announcement-wrapper' ).on( 'click', 'a.remove_announcement', function(e) {
            e.preventDefault();

            if( confirm( waa.delete_confirm ) ) {

                var self = $(this),
                    data = {
                        'action' : 'waa_announcement_remove_row',
                        'row_id' : self.data('notice_row'),
                        '_wpnonce' : waa.nonce
                    };
                self.closest('.waa-announcement-wrapper-item').append('<span class="waa-loading" style="position:absolute;top:2px; right:15px"> </span>');
                var row_count = $('.waa-announcement-wrapper-item').length;
                $.post( waa.ajaxurl, data, function(response) {
                    if( response.success ) {
                        self.closest('.waa-announcement-wrapper-item').find( 'span.waa-loading' ).remove();
                        self.closest('.waa-announcement-wrapper-item').fadeOut(function(){
                            $(this).remove();
                            if( row_count == 1 ) {
                                $( '.waa-announcement-wrapper' ).html( response.data );
                            }
                        });
                    } else {
                        alert( waa.wrong_message );
                    }
                });
            }

        });
    });

})(jQuery);
//waa store seo form submit
(function($){

    var wrapper = $( '.waa-dashboard-content.waa-settings-content.waa-store-seo-wrapper' );
    var waa_Store_SEO = {

        init : function() {
            wrapper.on( 'click', 'input#waa-store-seo-form-submit', this.form.validate );
        },
        form : {

            validate : function(){
                var self = $( this ),
                data = {
                    action: 'waa_seo_form_handler',
                    data: self.closest( '#waa-store-seo-form' ).serialize(),
                };
                console.log(data.data);
                waa_Store_SEO.form.submit( data );

                return false;
            },

            submit : function( data ){
                var feedback = $('#waa-seo-feedback');
                feedback.fadeOut();

                $.post( waa.ajaxurl, data, function ( resp ) {
                    if ( resp.success == true ) {
                        feedback.html(resp.data);
                        feedback.removeClass('waa-hide');
                        feedback.addClass('waa-alert-success');
                        feedback.fadeIn();
                    } else {
                        feedback.html(resp.data);
                        feedback.addClass('waa-alert-danger');
                        feedback.removeClass('waa-hide');
                        feedback.fadeIn();
                    }
                } )
            }

        },
    };

    $(function() {
        waa_Store_SEO.init();
    });

})(jQuery);

//localize Validation messages
(function($){
    var waa_messages = waaValidateMsg;

    waa_messages.maxlength   = $.validator.format( waa_messages.maxlength_msg );
    waa_messages.minlength   = $.validator.format( waa_messages.minlength_msg );
    waa_messages.rangelength = $.validator.format( waa_messages.rangelength_msg );
    waa_messages.range       = $.validator.format( waa_messages.range_msg );
    waa_messages.max         = $.validator.format( waa_messages.max_msg );
    waa_messages.min         = $.validator.format( waa_messages.min_msg );

    $.validator.messages = waa_messages;

    $(document).on('click','#waa_store_tnc_enable',function(e) {
        if($(this).is(':checked')) {
            $('#waa_tnc_text').show();
        }else {
            $('#waa_tnc_text').hide();
        }
    }).ready(function(e){
        if($('#waa_store_tnc_enable').is(':checked')) {
            $('#waa_tnc_text').show();
        }else {
            $('#waa_tnc_text').hide();
        }
    });

})(jQuery);
