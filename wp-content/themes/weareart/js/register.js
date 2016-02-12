jQuery(document).ready(function($) {
    $('#waa_user_login').bind('keypress', function (event) {
        var regex = new RegExp("^[a-zA-Z0-9_-]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            $('.tooltip').find('.tooltip-inner').css('background-color', 'red');
            $('.tooltip.top').find('.tooltip-arrow').css('border-top-color', 'red');
            return false;
        }
    });

    $('#region-select-container').hide();
    $('#waa_user_country').change(function() {
        var selected_country = $(this).val();

        if(selected_country) {
            // get regions for selected country
            if(selected_country == "CH")
                $('#region-select-container').find('> label').html('Kanton wählen');
            $.ajax({
                url:    'http://localhost/weart/wp-content/themes/weareart/js/'+selected_country+".regions.json",
                dataType: 'json',
                success: function( data ) {

                    var items = [ "<option value=''>Bitte wählen</option>" ];
                    $.each( data, function( code, region ) {
                        items.push( "<option value='" + region + "'>" + region + "</option>" );
                    });

                    var select = $( "<select/>", {
                        "id": "waa_user_region",
                        "name": "waa_user_region",
                        "required": "required",
                        html: items.join( "" )
                    });
                    $( "#waa_user_region_field" ).html(select);

                    // show regions-select
                    $('#region-select-container').show();
                },
                error: function ( data ) {
                    $('#region-select-container').hide();
                    alert('Noch keine Regionen für '+selected_country+' hinterlegt.');
                }
            });
        }
    });
    $('[data-toggle="tooltip"]').tooltip();

    var $waa_registration_form = $('#waa_registration_form');
    $waa_registration_form.find('input[type="submit"]').on('click', function(e) {
        $waa_registration_form.find('.required' ).each(function() {
            if(!$(this).val()) {
                $(this).parent().addClass("has-error");
                e.preventDefault();
                window.scrollTo(0,0);
                var $waa_js_errors = $('.waa_js_errors');
                if($(this).attr('data-error-msg'))
                    $waa_js_errors.find('.error-msg').text($(this).attr('data-error-msg'));
                else
                    $waa_js_errors.find('.error-msg').text("Bitte füllen Sie alle markierten Felder korrekt aus.");
                $waa_js_errors.show();
            }
            $(this).on('change keyup', function () {
                if($(this).val()) {
                    $(this).parent().removeClass("has-error");
                }
            });
        });
        $waa_registration_form.find('input[type="checkbox"].required').each(function() {
            if(!$(this).is(':checked')) {
                $(this).parent().parent().addClass("has-error");
                e.preventDefault();
                window.scrollTo(0,0);
            }
            $(this).on('change', function () {
                if($(this).is(':checked')) {
                    $(this).parent().parent().removeClass("has-error");
                }
            });
        });
    });
    $('#password, #password_again').on('change keyup', function () {
        var $passwd = $('#password');
        var $submit_btn = $waa_registration_form.find("input[type='submit']");
        if($passwd.val() === $('#password_again').val() && $passwd.val().length > 5) {
            $submit_btn.removeAttr("disabled").removeClass('disabled');
            $submit_btn.removeAttr("data-toggle");
            $submit_btn.removeAttr("title");
        } else {
            $submit_btn.attr("disabled", "disabled").addClass('disabled');
            $submit_btn.attr({"data-toggle": "tooltip", "data-placement": "top", "title": "Bitte stellen Sie sicher, dass die eingegebenen Passwörter übereinstimmen und min. 6 Zeichen lang sind." });

        }
    });
});