<?php
$store_user    = get_userdata( get_query_var( 'author' ) );
$store_info    = waa_get_store_info( $store_user->ID );
?>
<?php if ( isset( $store_info['banner'] ) && !empty( $store_info['banner'] ) ) { 
$banner_src = wp_get_attachment_image_src( $store_info['banner'], array(1200,1200) );
?>
<div class="profile-frame">
    <img src="<?= $banner_src[0] ?>">
</div> <!-- .profile-frame -->
</div>
<?php } ?>