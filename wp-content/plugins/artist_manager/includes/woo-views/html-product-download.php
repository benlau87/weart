<?php
if ( waa_get_option( 'product_style', 'waa_selling', 'old' ) == 'old' ) {
    ?>
    <tr>
        <td>
            <p>
                <label><?php _e( 'File Name:', 'waa' ); ?> <span class="tips" title="<?php _e( 'This is the name of the download shown to the customer.', 'waa' ); ?>">[?]</span></label>
                <input type="text" class="input_text" placeholder="<?php _e( 'File Name', 'waa' ); ?>" name="_wc_file_names[]" value="<?php echo esc_attr( $file['name'] ); ?>" />
            </p>

            <p>
                <label><?php _e( 'File URL:', 'waa' ); ?> <span class="tips" title="<?php _e( 'This is the URL or absolute path to the file which customers will get access to.', 'woocommerce' ); ?>">[?]</span></label>
                <input type="text" class="input_text wc_file_url" placeholder="<?php _e( "http://", 'waa' ); ?>" name="_wc_file_urls[]" value="<?php echo esc_attr( $file['file'] ); ?>" />
            </p>

            <p>
                <a href="#" class="waa-btn waa-btn-sm waa-btn-default upload_file_button" data-choose="<?php _e( 'Choose file', 'waa' ); ?>" data-update="<?php _e( 'Insert file URL', 'waa' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'woocommerce' ) ); ?></a>
                <a href="#" class="waa-btn waa-btn-sm waa-btn-danger delete"><span><?php _e( 'Delete', 'waa' ); ?></span></a>
            </p>
        </td>
    </tr>
    <?php
} elseif ( waa_get_option( 'product_style', 'waa_selling', 'old' ) == 'new' ) {
    ?>
    <tr>
        <td>
            <input type="text" class="input_text" placeholder="<?php _e( 'File Name', 'waa' ); ?>" name="_wc_file_names[]" value="<?php echo esc_attr( $file['name'] ); ?>" />

        </td>
        <td>
            <p>
                <input type="text" class="input_text wc_file_url" placeholder="<?php _e( "http://", 'waa' ); ?>" name="_wc_file_urls[]" value="<?php echo esc_attr( $file['file'] ); ?>" />
                <a href="#" class="waa-btn waa-btn-sm waa-btn-default upload_file_button" data-choose="<?php _e( 'Choose file', 'waa' ); ?>" data-update="<?php _e( 'Insert file URL', 'waa' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'waa' ) ); ?></a>
            </p>
        </td>

        <td>
            <p>
                <a href="#" class="waa-btn waa-btn-sm waa-btn-danger delete"><span><?php _e( 'Delete', 'waa' ); ?></span></a>
            </p>
        </td>
    </tr>
    <?php
}
?>
