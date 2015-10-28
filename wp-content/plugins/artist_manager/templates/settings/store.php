<?php
$waa_template_settings = waa_Template_Settings::init();
$validate                = $waa_template_settings->validate();

if ( $validate !== false && !is_wp_error( $validate ) ) {
   $waa_template_settings->insert_settings_info();
}
$current_user = get_current_user_id();

$scheme = is_ssl() ? 'https' : 'http';
wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?sensor=true' );
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings/store' ) ); ?>

    <div class="waa-dashboard-content waa-settings-content">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e( 'Settings', 'waa' );?>
                    <small>&rarr; <a href="<?php echo waa_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'waa' ); ?></a></small>
                </h1>
            </header><!-- .waa-dashboard-header -->

            <?php if ( is_wp_error( $validate ) ) {
                $messages = $validate->get_error_messages();

                foreach( $messages as $message ) {
                    ?>
                    <div class="waa-alert waa-alert-danger" style="width: 40%; margin-left: 25%;">
                        <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                        <strong><?php echo $message; ?></strong>
                    </div>

                    <?php
                }
            } ?>

            <?php //$waa_template_settings->setting_field($validate); ?>
            <!--settings updated content-->
            <?php

            if ( isset( $_GET['message'] ) ) {
                ?>
                <div class="waa-alert waa-alert-success">
                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e( 'Your profile has been updated successfully!', 'waa' ); ?></strong>
                </div>
            <?php
            }

            $profile_info = waa_get_store_info( $current_user );

            $gravatar   = isset( $profile_info['gravatar'] ) ? absint( $profile_info['gravatar'] ) : 0;
            $banner     = isset( $profile_info['banner'] ) ? absint( $profile_info['banner'] ) : 0;
            $storename  = isset( $profile_info['store_name'] ) ? esc_attr( $profile_info['store_name'] ) : '';
            $phone      = isset( $profile_info['phone'] ) ? esc_attr( $profile_info['phone'] ) : '';
            $description      = isset( $profile_info['description'] ) ? esc_attr( $profile_info['description'] ) : '';
            $website      = isset( $profile_info['website'] ) ? esc_attr( $profile_info['website'] ) : '';
            $show_email = isset( $profile_info['show_email'] ) ? esc_attr( $profile_info['show_email'] ) : 'no';

            $address         = isset( $profile_info['address'] ) ? $profile_info['address'] : '';
            $address_street1 = isset( $profile_info['address']['street_1'] ) ? $profile_info['address']['street_1'] : '';
            $address_street2 = isset( $profile_info['address']['street_2'] ) ? $profile_info['address']['street_2'] : '';
            $address_city    = isset( $profile_info['address']['city'] ) ? $profile_info['address']['city'] : '';
            $address_zip     = isset( $profile_info['address']['zip'] ) ? $profile_info['address']['zip'] : '';
            $address_country = isset( $profile_info['address']['country'] ) ? $profile_info['address']['country'] : '';
            $address_state   = isset( $profile_info['address']['state'] ) ? $profile_info['address']['state'] : '';

            $map_location   = isset( $profile_info['location'] ) ? esc_attr( $profile_info['location'] ) : '';
            $map_address    = isset( $profile_info['find_address'] ) ? esc_attr( $profile_info['find_address'] ) : '';
            $waa_category = isset( $profile_info['waa_category'] ) ? $profile_info['waa_category'] : '';
            $enable_tnc     = isset( $profile_info['enable_tnc'] ) ? $profile_info['enable_tnc'] : '';
            $store_tnc      = isset( $profile_info['store_tnc'] ) ? $profile_info['store_tnc'] : '' ;

            if ( is_wp_error( $validate ) ) {
                $storename    = $_POST['waa_store_name'];
                $map_location = $_POST['location'];
                $map_address  = $_POST['find_address'];

                $address_street1 = $_POST['waa_address']['street_1'];
                $address_street2 = $_POST['waa_address']['street_2'];
                $address_city    = $_POST['waa_address']['city'];
                $address_zip     = $_POST['waa_address']['zip'];
                $address_country = $_POST['waa_address']['country'];
                $address_state   = $_POST['waa_address']['state'];
            }
            ?>

            <div class="waa-ajax-response">
                <?php echo waa_get_profile_progressbar(); ?>
            </div>

            <?php do_action( 'waa_settings_before_form', $current_user, $profile_info ); ?>

            <form method="post" id="store-form"  action="" class="waa-form-horizontal">

                <?php wp_nonce_field( 'waa_store_settings_nonce' ); ?>

                <div class="waa-banner">

                    <div class="image-wrap<?php echo $banner ? '' : ' waa-hide'; ?>">
                        <?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
                        <input type="hidden" class="waa-file-field" value="<?php echo $banner; ?>" name="waa_banner">
                        <img class="waa-banner-img" src="<?php echo esc_url( $banner_url ); ?>">

                        <a class="close waa-remove-banner-image">&times;</a>
                    </div>

                    <div class="button-area<?php echo $banner ? ' waa-hide' : ''; ?>">
                        <i class="fa fa-cloud-upload"></i>

                        <a href="#" class="waa-banner-drag waa-btn waa-btn-info waa-theme"><?php _e( 'Upload banner', 'waa' ); ?></a>
                        <p class="help-block"><?php _e( '(Upload a banner for your store. Banner size is (825x300) pixel. )', 'waa' ); ?></p>
                    </div>
                </div> <!-- .waa-banner -->

                <?php do_action( 'waa_settings_after_banner', $current_user, $profile_info ); ?>

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="waa_gravatar"><?php _e( 'Profile Picture', 'waa' ); ?></label>

                    <div class="waa-w5 waa-gravatar">
                        <div class="waa-left gravatar-wrap<?php echo $gravatar ? '' : ' waa-hide'; ?>">
                            <?php $gravatar_url = $gravatar ? wp_get_attachment_url( $gravatar ) : ''; ?>
                            <input type="hidden" class="waa-file-field" value="<?php echo $gravatar; ?>" name="waa_gravatar">
                            <img class="waa-gravatar-img" src="<?php echo esc_url( $gravatar_url ); ?>">
                            <a class="waa-close waa-remove-gravatar-image">&times;</a>
                        </div>
                        <div class="gravatar-button-area<?php echo $gravatar ? ' waa-hide' : ''; ?>">
                            <a href="#" class="waa-gravatar-drag waa-btn waa-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'waa' ); ?></a>
                        </div>
                    </div>
                </div>

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="waa_store_name"><?php _e( 'Store Name', 'waa' ); ?></label>

                    <div class="waa-w5 waa-text-left">
                        <input id="waa_store_name" required value="<?php echo $storename; ?>" name="waa_store_name" placeholder="<?php _e( 'store name', 'waa'); ?>" class="waa-form-control" type="text">
                    </div>
                </div>
                 <!--address-->

                <?php
                $verified = false;

                if ( isset( $profile_info['waa_verification']['info']['store_address']['v_status'] ) ) {
                    if ( $profile_info['waa_verification']['info']['store_address']['v_status'] == 'approved' ){
                        $verified = true;
                    }
                }
                waa_seller_address_fields( $verified );

                ?>
                <!--address-->

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="setting_phone"><?php _e( 'Phone No', 'waa' ); ?></label>
                    <div class="waa-w5 waa-text-left">
                        <input id="setting_phone" value="<?php echo $phone; ?>" name="setting_phone" placeholder="<?php _e( '+123456..', 'waa' ); ?>" class="waa-form-control input-md" type="text">
                    </div>
                </div>
								
								<div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="setting_description"><?php _e( 'Description', 'waa' ); ?></label>
                    <div class="waa-w5 waa-text-left">
                        <textarea id="setting_description" name="setting_description" placeholder="<?php _e( 'Description', 'waa' ); ?>" class="waa-form-control input-md"><?= $description; ?></textarea>
												<input type="hidden" name="setting_show_email" value="no">
                    </div>
                </div>
								
								<div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="setting_website"><?php _e( 'Website', 'waa' ); ?></label>
                    <div class="waa-w5 waa-text-left">
                        <input type="text" id="setting_website" name="setting_website" placeholder="<?php _e( 'Website', 'waa' ); ?>" class="waa-form-control input-md" value="<?= $website; ?>">
                    </div>
                </div>

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="setting_map"><?php _e( 'Map', 'waa' ); ?></label>

                    <div class="waa-w6 waa-text-left">
                        <input id="waa-map-lat" type="hidden" name="location" value="<?php echo $map_location; ?>" size="30" />

                        <div class="waa-map-wrap">
                            <div class="waa-map-search-bar">
                                <input id="waa-map-add" type="text" class="waa-map-search" value="<?php echo $map_address; ?>" name="find_address" placeholder="<?php _e( 'Type an address to find', 'waa' ); ?>" size="30" />
                                <a href="#" class="waa-map-find-btn" id="waa-location-find-btn" type="button"><?php _e( 'Find Address', 'waa' ); ?></a>
                            </div>

                            <div class="waa-google-map" id="waa-map"></div>
                        </div>
                    </div> <!-- col.md-4 -->
                </div> <!-- .waa-form-group -->

                <!--terms and conditions enable or not -->
                <?php
                $tnc_enable = waa_get_option( 'seller_enable_terms_and_conditions', 'waa_selling', 'off' );
                if ( $tnc_enable == 'on' ) :
                    ?>
                    <div class="waa-form-group">
                        <label class="waa-w3 waa-control-label" for="waa_store_tnc_enable"><?php _e( 'Terms and Conditions', 'waa' ); ?></label>
                        <div class="waa-w5 waa-text-left waa_tock_check">
                            <div class="checkbox">
                                <label>
                                    <input id="waa_store_tnc_enable" value="on" <?php echo $enable_tnc == 'on' ? 'checked':'' ; ?> name="waa_store_tnc_enable" class="waa-form-control" type="checkbox"><?php _e( 'Show terms and conditions in store page', 'waa' ); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="waa-form-group" id="waa_tnc_text">
                        <label class="waa-w3 waa-control-label" for="waa_store_tnc"><?php _e( 'TOC Details', 'waa' ); ?></label>
                        <div class="waa-w8 waa-text-left">
                            <?php
                            $settings = array(
                                'editor_height' => 200,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => false
                            );
                            wp_editor( $store_tnc, 'waa_store_tnc', $settings);
                            ?>
                        </div>
                    </div>

                <?php endif;?>


                <?php do_action( 'waa_settings_form_bottom', $current_user, $profile_info ); ?>

                <div class="waa-form-group">

                    <div class="waa-w4 ajax_prev waa-text-left" style="margin-left:24%;">
                        <input type="submit" name="waa_update_store_settings" class="waa-btn waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Update Settings', 'waa' ); ?>">
                    </div>
                </div>
            </form>

            <?php do_action( 'waa_settings_after_form', $current_user, $profile_info ); ?>

            <script type="text/javascript">

                (function($) {
                    var waa_address_wrapper = $( '.waa-address-fields' );
                    var waa_address_select = {
                        init: function () {

                            waa_address_wrapper.on( 'change', 'select.country_to_state', this.state_select );
                        },
                        state_select: function () {
                            var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
                                states = $.parseJSON( states_json ),
                                $statebox = $( '#waa_address_state' ),
                                input_name = $statebox.attr( 'name' ),
                                input_id = $statebox.attr( 'id' ),
                                input_class = $statebox.attr( 'class' ),
                                value = $statebox.val(),
                                selected_state = '<?php echo $address_state; ?>',
                                input_selected_state = '<?php echo $address_state; ?>',
                                country = $( this ).val();

                            if ( states[ country ] ) {

                                if ( $.isEmptyObject( states[ country ] ) ) {

                                    $( 'div#waa-states-box' ).slideUp( 2 );
                                    if ( $statebox.is( 'select' ) ) {
                                        $( 'select#waa_address_state' ).replaceWith( '<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required />' );
                                    }

                                    $( '#waa_address_state' ).val( 'N/A' );

                                } else {
                                    input_selected_state = '';

                                    var options = '',
                                        state = states[ country ];

                                    for ( var index in state ) {
                                        if ( state.hasOwnProperty( index ) ) {
                                            if ( selected_state ) {
                                                if ( selected_state == index ) {
                                                    var selected_value = 'selected="selected"';
                                                } else {
                                                    var selected_value = '';
                                                }
                                            }
                                            options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
                                        }
                                    }

                                    if ( $statebox.is( 'select' ) ) {
                                        $( 'select#waa_address_state' ).html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                                    }
                                    if ( $statebox.is( 'input' ) ) {
                                        $( 'input#waa_address_state' ).replaceWith( '<select type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required ></select>' );
                                        $( 'select#waa_address_state' ).html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                                    }
                                    $( '#waa_address_state' ).removeClass( 'waa-hide' );
                                    $( 'div#waa-states-box' ).slideDown();

                                }
                            } else {


                                if ( $statebox.is( 'select' ) ) {
                                    input_selected_state = '';
                                    $( 'select#waa_address_state' ).replaceWith( '<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required="required"/>' );
                                }
                                $( '#waa_address_state' ).val(input_selected_state);

                                if ( $( '#waa_address_state' ).val() == 'N/A' ){
                                    $( '#waa_address_state' ).val('');
                                }
                                $( '#waa_address_state' ).removeClass( 'waa-hide' );
                                $( 'div#waa-states-box' ).slideDown();
                            }
                        }
                    }

                    $(function() {
                        waa_address_select.init();

                        <?php
                        $locations = explode( ',', $map_location );
                        $def_lat = isset( $locations[0] ) ? $locations[0] : 90.40714300000002;
                        $def_long = isset( $locations[1] ) ? $locations[1] : 23.709921;
                        ?>
                        var def_zoomval = 12;
                        var def_longval = '<?php echo $def_long; ?>';
                        var def_latval = '<?php echo $def_lat; ?>';
                        var curpoint = new google.maps.LatLng(def_latval, def_longval),
                            geocoder   = new window.google.maps.Geocoder(),
                            $map_area = $('#waa-map'),
                            $input_area = $( '#waa-map-lat' ),
                            $input_add = $( '#waa-map-add' ),
                            $find_btn = $( '#waa-location-find-btn' );

                        autoCompleteAddress();

                        $find_btn.on('click', function(e) {
                            e.preventDefault();

                            geocodeAddress( $input_add.val() );
                        });

                        var gmap = new google.maps.Map( $map_area[0], {
                            center: curpoint,
                            zoom: def_zoomval,
                            mapTypeId: window.google.maps.MapTypeId.ROADMAP
                        });

                        var marker = new window.google.maps.Marker({
                            position: curpoint,
                            map: gmap,
                            draggable: true
                        });

                        window.google.maps.event.addListener( gmap, 'click', function ( event ) {
                            marker.setPosition( event.latLng );
                            updatePositionInput( event.latLng );
                        } );

                        window.google.maps.event.addListener( marker, 'drag', function ( event ) {
                            updatePositionInput(event.latLng );
                        } );

                        function updatePositionInput( latLng ) {
                            $input_area.val( latLng.lat() + ',' + latLng.lng() );
                        }

                        function updatePositionMarker() {
                            var coord = $input_area.val(),
                                pos, zoom;

                            if ( coord ) {
                                pos = coord.split( ',' );
                                marker.setPosition( new window.google.maps.LatLng( pos[0], pos[1] ) );

                                zoom = pos.length > 2 ? parseInt( pos[2], 10 ) : 12;

                                gmap.setCenter( marker.position );
                                gmap.setZoom( zoom );
                            }
                        }

                        function geocodeAddress( address ) {
                            geocoder.geocode( {'address': address}, function ( results, status ) {
                                if ( status == window.google.maps.GeocoderStatus.OK ) {
                                    updatePositionInput( results[0].geometry.location );
                                    marker.setPosition( results[0].geometry.location );
                                    gmap.setCenter( marker.position );
                                    gmap.setZoom( 15 );
                                }
                            } );
                        }

                        function autoCompleteAddress(){
                            if (!$input_add) return null;

                            $input_add.autocomplete({
                                source: function(request, response) {
                                    // TODO: add 'region' option, to help bias geocoder.
                                    geocoder.geocode( {'address': request.term }, function(results, status) {
                                        response(jQuery.map(results, function(item) {
                                            return {
                                                label     : item.formatted_address,
                                                value     : item.formatted_address,
                                                latitude  : item.geometry.location.lat(),
                                                longitude : item.geometry.location.lng()
                                            };
                                        }));
                                    });
                                },
                                select: function(event, ui) {

                                    $input_area.val(ui.item.latitude + ',' + ui.item.longitude );

                                    var location = new window.google.maps.LatLng(ui.item.latitude, ui.item.longitude);

                                    gmap.setCenter(location);
                                    // Drop the Marker
                                    setTimeout( function(){
                                        marker.setValues({
                                            position    : location,
                                            animation   : window.google.maps.Animation.DROP
                                        });
                                    }, 1500);
                                }
                            });
                        }

                    });
                })(jQuery);
            </script>

            <!--settings updated content ends-->
        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->