<?php
/**
 * User profile related tasks for wp-admin
 *
 * @package waa
 */
class waa_Admin_User_Profile {

    public function __construct() {
        add_action( 'show_user_profile', array( $this, 'add_meta_fields' ), 20 );
        add_action( 'edit_user_profile', array( $this, 'add_meta_fields' ), 20 );

        add_action( 'personal_options_update', array( $this, 'save_meta_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_meta_fields' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    function enqueue_scripts( $page ) {
        if ( in_array( $page, array( 'profile.php', 'user-edit.php' )) ) {
            wp_enqueue_media();
        }
    }

    /**
     * Add fields to user profile
     *
     * @param WP_User $user
     * @return void|false
     */
    function add_meta_fields( $user ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( !user_can( $user, 'dokandar' ) ) {
            return;
        }

        $selling           = get_user_meta( $user->ID, 'waa_enable_selling', true );
        $publishing        = get_user_meta( $user->ID, 'waa_publishing', true );
        $store_settings    = waa_get_store_info( $user->ID );
        $banner            = isset( $store_settings['banner'] ) ? absint( $store_settings['banner'] ) : 0;
        $seller_percentage = get_user_meta( $user->ID, 'waa_seller_percentage', true );
        $feature_seller    = get_user_meta( $user->ID, 'waa_feature_seller', true );

        $social_fields     = waa_get_social_profile_fields();

        ?>
        <h3><?php _e( 'waa Options', 'waa' ); ?></h3>

        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php _e( 'Banner', 'waa' ); ?></th>
                    <td>
                        <div class="waa-banner">
                            <div class="image-wrap<?php echo $banner ? '' : ' waa-hide'; ?>">
                                <?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
                                <input type="hidden" class="waa-file-field" value="<?php echo $banner; ?>" name="waa_banner">
                                <img class="waa-banner-img" src="<?php echo esc_url( $banner_url ); ?>">

                                <a class="close waa-remove-banner-image">&times;</a>
                            </div>

                            <div class="button-area<?php echo $banner ? ' waa-hide' : ''; ?>">
                                <a href="#" class="waa-banner-drag button button-primary"><?php _e( 'Upload banner', 'waa' ); ?></a>
                                <p class="description"><?php _e( '(Upload a banner for your store. Banner size is (825x300) pixel. )', 'waa' ); ?></p>
                            </div>
                        </div> <!-- .waa-banner -->
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Store name', 'waa' ); ?></th>
                    <td>
                        <input type="text" name="waa_store_name" class="regular-text" value="<?php echo esc_attr( $store_settings['store_name'] ); ?>">
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Address', 'waa' ); ?></th>
                    <td>
                        <textarea name="waa_store_address" rows="4" cols="30"><?php echo esc_textarea( $store_settings['address'] ); ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Phone', 'waa' ); ?></th>
                    <td>
                        <input type="text" name="waa_store_phone" class="regular-text" value="<?php echo esc_attr( $store_settings['phone'] ); ?>">
                    </td>
                </tr>
								
								<tr>
                    <th><?php _e( 'Description', 'waa' ); ?></th>
                    <td>
                        <textarea name="waa_store_description"><?php echo esc_attr( $store_settings['description'] ); ?></textarea>
                    </td>
                </tr>

                <?php foreach ($social_fields as $key => $value) { ?>

                    <tr>
                        <th><?php echo $value['title']; ?></th>
                        <td>
                            <input type="text" name="waa_social[<?php echo $key; ?>]" class="regular-text" value="<?php echo isset( $store_settings['social'][$key] ) ? esc_url( $store_settings['social'][$key] ) : ''; ?>">
                        </td>
                    </tr>

                <?php } ?>

                <tr>
                    <th><?php _e( 'Selling', 'waa' ); ?></th>
                    <td>
                        <label for="waa_enable_selling">
                            <input type="hidden" name="waa_enable_selling" value="no">
                            <input name="waa_enable_selling" type="checkbox" id="waa_enable_selling" value="yes" <?php checked( $selling, 'yes' ); ?> />
                            <?php _e( 'Enable Selling', 'waa' ); ?>
                        </label>

                        <p class="description"><?php _e( 'Enable or disable product selling capability', 'waa' ) ?></p>
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Publishing', 'waa' ); ?></th>
                    <td>
                        <label for="waa_publish">
                            <input type="hidden" name="waa_publish" value="no">
                            <input name="waa_publish" type="checkbox" id="waa_publish" value="yes" <?php checked( $publishing, 'yes' ); ?> />
                            <?php _e( 'Publish product directly', 'waa' ); ?>
                        </label>

                        <p class="description"><?php _e( 'Instead going pending, products will be published directly', 'waa' ) ?></p>
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Seller Percentage', 'waa' ); ?></th>
                    <td>
                        <input type="text" class="small-text" name="waa_seller_percentage" value="<?php echo esc_attr( $seller_percentage ); ?>">

                        <p class="description"><?php _e( 'How much amount (%) will get from each order', 'waa' ) ?></p>
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Feature Seller', 'WAA' ); ?></th>
                    <td>
                        <label for="waa_feature">
                            <input type="hidden" name="waa_feature" value="no">
                            <input name="waa_feature" type="checkbox" id="waa_feature" value="yes" <?php checked( $feature_seller, 'yes' ); ?> />
                            <?php _e( 'Make feature seller', 'WAA' ); ?>
                        </label>

                        <p class="description"><?php _e( 'This seller will be marked as a feature seller.', 'WAA' ) ?></p>
                    </td>
                </tr>

                <?php do_action( 'waa_seller_meta_fields', $user ); ?>

            </tbody>
        </table>

        <style type="text/css">
        .waa-hide { display: none; }
        .button-area { padding-top: 100px; }
        .waa-banner {
            border: 4px dashed #d8d8d8;
            height: 255px;
            margin: 0;
            overflow: hidden;
            position: relative;
            text-align: center;
            max-width: 700px;
        }
        .waa-banner img { max-width:100%; }
        .waa-banner .waa-remove-banner-image {
            position:absolute;
            width:100%;
            height:270px;
            background:#000;
            top:0;
            left:0;
            opacity:.7;
            font-size:100px;
            color:#f00;
            padding-top:70px;
            display:none
        }
        .waa-banner:hover .waa-remove-banner-image {
            display:block;
            cursor: pointer;
        }
        </style>

        <script type="text/javascript">
        jQuery(function($){
            var waa_Settings = {

                init: function() {
                    $('a.waa-banner-drag').on('click', this.imageUpload);
                    $('a.waa-remove-banner-image').on('click', this.removeBanner);
                },

                imageUpload: function(e) {
                    e.preventDefault();

                    var file_frame,
                        self = $(this);

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

                    file_frame.on( 'select', function() {
                        var attachment = file_frame.state().get('selection').first().toJSON();

                        var wrap = self.closest('.waa-banner');
                        wrap.find('input.waa-file-field').val(attachment.id);
                        wrap.find('img.waa-banner-img').attr('src', attachment.url);
                        $('.image-wrap', wrap).removeClass('waa-hide');

                        $('.button-area').addClass('waa-hide');
                    });

                    file_frame.open();

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
            };

            waa_Settings.init();
        });
        </script>
        <?php
    }

    /**
     * Save user data
     *
     * @param int $user_id
     * @return void
     */
    function save_meta_fields( $user_id ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( ! isset( $_POST['waa_enable_selling'] ) ) {
            return;
        }

        $selling        = sanitize_text_field( $_POST['waa_enable_selling'] );
        $publishing     = sanitize_text_field( $_POST['waa_publish'] );
        $percentage     = floatval( $_POST['waa_seller_percentage'] );
        $feature_seller = sanitize_text_field( $_POST['waa_feature'] );
        $store_settings = waa_get_store_info( $user_id );

        $social         = $_POST['waa_social'];
        $social_fields  = waa_get_social_profile_fields();

        $store_settings['banner']     = intval( $_POST['waa_banner'] );
        $store_settings['store_name'] = sanitize_text_field( $_POST['waa_store_name'] );
        $store_settings['address']    = wp_kses_post( $_POST['waa_store_address'] );
        $store_settings['phone']      = sanitize_text_field( $_POST['waa_store_phone'] );
        $store_settings['description']      = sanitize_text_field( $_POST['waa_store_description'] );

        // social settings
        if ( is_array( $social ) ) {
            foreach ($social as $key => $value) {
                if ( isset( $social_fields[ $key ] ) ) {
                    $store_settings['social'][ $key ] = filter_var( $social[ $key ], FILTER_VALIDATE_URL );
                }
            }
        }

        update_user_meta( $user_id, 'waa_profile_settings', $store_settings );
        update_user_meta( $user_id, 'waa_enable_selling', $selling );
        update_user_meta( $user_id, 'waa_publishing', $publishing );
        update_user_meta( $user_id, 'waa_seller_percentage', $percentage );
        update_user_meta( $user_id, 'waa_feature_seller', $feature_seller );

        do_action( 'waa_process_seller_meta_fields', $user_id );
    }
}