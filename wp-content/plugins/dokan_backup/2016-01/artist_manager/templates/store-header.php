<?php
$store_user    = get_userdata( get_query_var( 'author' ) );
$store_info    = waa_get_store_info( $store_user->ID );
?>
<?php if ( isset( $store_info['banner'] ) && !empty( $store_info['banner'] ) ) { 
$banner_src = wp_get_attachment_image_src( $store_info['banner'], array(1200,1200) );
?>
<div class="profile-frame">
    <style type="text/css">
        .profile-frame {
            background-image: url('<?= $banner_src[0] ?>');
        }
    </style>

    <?php if ( $store_tabs ) { ?>
        <div class="waa-store-tabs">
            <ul class="waa-list-inline">
                <?php foreach( $store_tabs as $key => $tab ) { ?>
                    <li><a href="<?php echo esc_url( $tab['url'] ); ?>"><?php echo $tab['title']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div> <!-- .profile-frame -->
</div>
<?php } ?>