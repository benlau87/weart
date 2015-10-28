<div class="wrap">
    <h2><?php _e( 'waa Tools', 'waa' ); ?></h2>

    <?php
    $msg = isset( $_GET['msg'] ) ? $_GET['msg'] : '';
    $text = '';

    switch ($msg) {
        case 'page_installed':
            $text = __( 'Pages have been created and installed!', 'waa' );
            break;

        case 'regenerated':
            $text = __( 'Order sync table has been regenerated!', 'waa' );
            break;
    }

    if ( $text ) {
        ?>
        <div class="updated">
            <p>
                <?php echo $text; ?>
            </p>
        </div>

    <?php } ?>

    <div class="metabox-holder">
        <div class="postbox">
            <h3><?php _e( 'Page Installation', 'waa' ); ?></h3>

            <div class="inside">
                <p><?php _e( 'Clicking this button will create required pages for the plugin.', 'waa' ); ?></p>
                <a class="button button-primary" href="<?php echo wp_nonce_url( add_query_arg( array( 'waa_action' => 'waa_install_pages' ), 'admin.php?page=waa-tools' ), 'waa-tools-action' ); ?>"><?php _e( 'Install waa Pages', 'waa' ); ?></a>
            </div>
        </div>

        <div class="postbox">
            <h3><?php _e( 'Regenerate Order Sync Table', 'waa' ); ?></h3>

            <div class="inside">
                <p><?php _e( 'This tool will delete all orders from the waa\'s sync table and re-build it.', 'waa' ); ?></p>

                <a class="button button-primary" href="<?php echo wp_nonce_url( add_query_arg( array( 'waa_action' => 'regen_sync_table' ), 'admin.php?page=waa-tools' ), 'waa-tools-action' ); ?>" onclick="return confirm('Are you sure?');"><?php _e( 'Re-build', 'waa' ); ?></a>
            </div>
        </div>
    </div>
</div>