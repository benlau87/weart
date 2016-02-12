<?php
global $post, $product;

wp_enqueue_script( 'waa-tabs-scripts' );

$post_id        = $post->ID;
$seller_id      = get_current_user_id();
$from_shortcode = false;

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $product        = wc_get_product( $post_id );
    $from_shortcode = true;
}

// bail out if not author
if ( $post->post_author != $seller_id ) {
    wp_die( __( 'Access Denied', 'waa' ) );
}

$_regular_price         = get_post_meta( $post_id, '_regular_price', true );
$_sale_price            = get_post_meta( $post_id, '_sale_price', true );
$is_discount            = ( $_sale_price != '' ) ? true : false;
$_sale_price_dates_from = get_post_meta( $post_id, '_sale_price_dates_from', true );
$_sale_price_dates_to   = get_post_meta( $post_id, '_sale_price_dates_to', true );

$_sale_price_dates_from = !empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : '';
$_sale_price_dates_to   = !empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : '';
$show_schedule          = false;

if ( !empty( $_sale_price_dates_from ) && !empty( $_sale_price_dates_to ) ) {
    $show_schedule = true;
}

$_featured       = get_post_meta( $post_id, '_featured', true );
$_weight         = get_post_meta( $post_id, '_weight', true );
$_length         = get_post_meta( $post_id, '_length', true );
$_width          = get_post_meta( $post_id, '_width', true );
$_height         = get_post_meta( $post_id, '_height', true );
$_downloadable   = get_post_meta( $post_id, '_downloadable', true );
$_stock_status   = get_post_meta( $post_id, '_stock_status', true );
$_visibility     = get_post_meta( $post_id, '_visibility', true );
$_enable_reviews = $post->comment_status;

if ( ! $from_shortcode ) {
    get_header();
}
?>
<section id="content" role="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12" role="main">	
				<div class="waa-dashboard-wrap">
						<?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'product' ) ); ?>

						<div class="waa-dashboard-content waa-product-edit">
								<div class="waa-product-edit-area">

										<header class="waa-pro-edit-breadcrumb">
												<h1 class="waa-header-crumb">
														<span class="waa-breadcrumb"><a href="<?php echo waa_get_navigation_url( 'products' ) ?>"><?php _e( 'Products', 'waa' ); ?></a> &rarr; </span>
														<?php echo $post->post_title; ?>

														<?php if ( $_visibility == 'hidden' ) { ?>
																<span class="waa-label waa-label-default"><?php _e( 'Hidden', 'waa' ); ?></span>
														<?php } ?>

														<?php if ( $post->post_status == 'publish' ) { ?>
																<span class="waa-right">
																		<a class="view-product waa-btn waa-btn-sm" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank"><?php _e( 'View Product', 'waa' ); ?></a>
																</span>
														<?php } ?>
												</h1>
										</header>

										<form class="waa-form-container" role="form" method="post">
												<?php wp_nonce_field( 'waa_edit_product', 'waa_edit_product_nonce' ); ?>
												<div class="product-edit-container waa-clearfix">

														<div class="waa-product-edit-left">

																<?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success') { ?>
																		<div class="waa-message">
																				<button type="button" class="waa-close" data-dismiss="alert">&times;</button>
																				<strong><?php _e( 'Success!', 'waa' ); ?></strong> <?php _e( 'The product has been updated successfully.', 'waa' ); ?>

																				<?php if ( $post->post_status == 'publish' ) { ?>
																						<a href="<?php echo get_permalink( $post_id ); ?>" target="_blank"><?php _e( 'View Product &rarr;', 'waa' ); ?></a>
																				<?php } ?>
																		</div>
																<?php } ?>
																<?php if ( waa_Template_Shortcodes::$errors ) { ?>
																		<div class="waa-alert waa-alert-danger">
																				<a class="waa-close" data-dismiss="alert">&times;</a>

																				<?php foreach ( waa_Template_Shortcodes::$errors as $error) { ?>

																						<strong><?php _e( 'Error!', 'waa' ); ?></strong> <?php echo $error ?>.<br>

																				<?php } ?>
																		</div>
																<?php } ?>

																<div id="tab-container" class='tab-container'> <!-- Only required for left/right tabs -->

																		<ul class="waa_tabs">
																				<?php
																				$terms                   = wp_get_object_terms( $post->ID, 'product_type' );
																				$product_type            = sanitize_title( current( $terms )->name );
																				$variations_class        = ($product_type == 'simple' ) ? 'waa-hide' : '';
																				$waa_product_data_tabs = apply_filters( 'waa_product_data_tabs', array(

																						'edit' => array(
																								'label'  => __( 'Edit', 'waa' ),
																								'target' => 'edit-product',
																								'class'  => array( 'active' ),
																						),
																						'options' => array(
																								'label'  => __( 'Options', 'waa' ),
																								'target' => 'product-options',
																								'class'  => array(),
																						),
																						'inventory' => array(
																								'label'  => __( 'Inventory', 'waa' ),
																								'target' => 'product-inventory',
																								'class'  => array(),
																						),
																						'shipping' => array(
																								'label'  => __( 'Shipping', 'waa' ),
																								'target' => 'product-shipping',
																								'class'  => array(),
																						),
																						'attributes' => array(
																								'label'  => __( 'Attributes', 'waa' ),
																								'target' => 'product-attributes',
																								'class'  => array(),
																						),
																						'variations' => array(
																								'label'  => __( 'Variations', 'waa' ),
																								'target' => 'product-variations',
																								'class'  => array( 'show_if_variable', $variations_class ),
																						),

																				) );

																				foreach ( $waa_product_data_tabs as $key => $tab ) { ?>
																						<li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
																								<a href="#<?php echo $tab['target']; ?>" data-toggle="tab"><?php echo esc_html( $tab['label'] ); ?></a>
																						</li>
																				<?php
																				}

																				do_action( 'waa_product_data_panel_tabs' );
																				?>

																		</ul>

																		<div id="tabs_container">
																				<div id="edit-product">

																						<?php do_action( 'waa_product_edit_before_main' ); ?>

																						<div class="waa-clearfix">
																								<div class="content-half-part">
																										<div class="waa-feat-image-upload">
																												<?php
																												$wrap_class        = ' waa-hide';
																												$instruction_class = '';
																												$feat_image_id     = 0;

																												if ( has_post_thumbnail( $post_id ) ) {
																														$wrap_class        = '';
																														$instruction_class = ' waa-hide';
																														$feat_image_id     = get_post_thumbnail_id( $post_id );
																												}
																												?>

																												<div class="instruction-inside<?php echo $instruction_class; ?>">
																														<input type="hidden" name="feat_image_id" class="waa-feat-image-id" value="<?php echo $feat_image_id; ?>">

																														<i class="ui ui-cloud-upload"></i>
																														<a href="#" class="waa-feat-image-btn btn btn-sm"><?php _e( 'Upload a product cover image', 'waa' ); ?></a>
																												</div>

																												<div class="image-wrap<?php echo $wrap_class; ?>">
																														<a class="close waa-remove-feat-image">&times;</a>
																														<?php if ( $feat_image_id ) { ?>
																																<?php echo get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array( 'height' => '', 'width' => '' ) ); ?>
																														<?php } else { ?>
																																<img height="" width="" src="" alt="">
																														<?php } ?>
																												</div>
																										</div>
																								</div>

																								<div class="content-half-part">

																										<div class="waa-form-group">
																												<input type="hidden" name="waa_product_id" value="<?php echo $post_id; ?>">
																												<?php waa_post_input_box( $post_id, 'post_title', array( 'placeholder' => 'Product name..', 'value' => $post->post_title ) ); ?>
																										</div>

																										<div class="show_if_simple waa-clearfix">
																												<div class="waa-form-group">
																														<div class="waa-input-group">
																																<span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
																																<?php waa_post_input_box( $post_id, '_regular_price', array( 'placeholder' => '0.00' ) ); ?>
																														</div>
																												</div>


																												<div class="discount-price">
																														<label>
																																<input type="checkbox" <?php checked( $is_discount, true ); ?> class="_discounted_price" name="_discounted_price"> <?php _e( 'Enable Discounted Price', 'waa' ); ?>
																														</label>
																												</div>
																										</div>

																										<div class="show_if_simple">
																												<div class="special-price-container<?php echo $is_discount ? '' : ' waa-hide'; ?>">
																														<div class="waa-form-group waa-clearfix">
																																<div class="waa-input-group waa-left" style="width:55%">
																																		<span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
																																		<?php waa_post_input_box( $post_id, '_sale_price', array( 'placeholder' => __( 'Special Price', 'waa' ) ) ); ?>
																																</div>

																																<div class="waa-right">
																																		<a href="#" class="sale-schedule waa-right"><?php _e( 'Schedule', 'waa' ); ?></a>
																																</div>
																														</div>

																														<div class="sale-schedule-container<?php echo $show_schedule ? '' : ' waa-hide'; ?>">
																																<div class="waa-form-group">
																																		<div class="waa-input-group">
																																				<span class="waa-input-group-addon"><?php _e( 'From', 'waa' ); ?></span>
																																				<input type="text" name="_sale_price_dates_from" class="waa-form-control datepicker" value="<?php echo esc_attr( $_sale_price_dates_from ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD">
																																		</div>
																																</div>

																																<div class="waa-form-group">
																																		<div class="waa-input-group">
																																				<span class="waa-input-group-addon"><?php _e( 'To', 'waa' ); ?></span>
																																				<input type="text" name="_sale_price_dates_to" class="waa-form-control datepicker" value="<?php echo esc_attr( $_sale_price_dates_to ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD">
																																		</div>
																																</div>
																														</div>
																												</div>
																										</div> <!-- .show_if_simple -->


																										<div class="waa-form-group">
																												<?php waa_post_input_box( $post_id, 'post_excerpt', array( 'placeholder' => 'Short description about the product...', 'value' => $post->post_excerpt ), 'textarea' ); ?>
																										</div>

																										<?php if ( waa_get_option( 'product_category_style', 'waa_selling', 'single' ) == 'single' ): ?>
																												<div class="waa-form-group">

																														<?php
																														$product_cat = -1;
																														$term = array();
																														$term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

																														if ( $term ) {
																																$product_cat = reset( $term );
																														}

																														wp_dropdown_categories( array(
																																'show_option_none' => __( '- Select a category -', 'waa' ),
																																'hierarchical'     => 1,
																																'hide_empty'       => 0,
																																'name'             => 'product_cat',
																																'id'               => 'product_cat',
																																'taxonomy'         => 'product_cat',
																																'title_li'         => '',
																																'class'            => 'product_cat waa-form-control chosen',
																																'exclude'          => '',
																																'selected'         => $product_cat,
																														) );
																														?>
																												</div>
																										<?php elseif ( waa_get_option( 'product_category_style', 'waa_selling', 'single' ) == 'multiple' ): ?>
																												<div class="waa-form-group waa-list-category-box">
																														<h5><?php _e( 'Choose a category', 'waa' );  ?></h5>
																														<ul class="waa-checkbox-cat">
																																<?php
																																$term = array();
																																$term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

																																include_once waa_LIB_DIR.'/class.category-walker.php';
																																wp_list_categories(array(
																																		'walker'       => new waaCategoryWalker(),
																																		'title_li'     => '',
																																		'id'           => 'product_cat',
																																		'hide_empty'   => 0,
																																		'taxonomy'     => 'product_cat',
																																		'hierarchical' => 1,
																																		'selected'     => $term
																																));
																																?>
																														</ul>
																												</div>
																										<?php endif; ?>

																										<div class="waa-form-group">

																														<?php

																														require_once waa_LIB_DIR.'/class.tag-walker.php';

																														$term = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'ids') );
																														$selected = ( $term ) ? $term : array();
																														$drop_down_tags = wp_dropdown_categories( array(
																																'show_option_none' => __( '', 'waa' ),
																																'hierarchical'     => 1,
																																'hide_empty'       => 0,
																																'name'             => 'product_tag[]',
																																'id'               => 'product_tag',
																																'taxonomy'         => 'product_tag',
																																'title_li'         => '',
																																'class'            => 'product_tags waa-form-control',
																																'exclude'          => '',
																																'selected'         => $selected,
																																'echo'             => 0,
																																'walker'           => new waa_Walker_Tag_Multi()
																														) );

																														echo str_replace( '<select', '<select data-placeholder="' . __( 'Select product tags', 'waa' ) . '" multiple="multiple" ', $drop_down_tags );

																														?>
																										</div>

																								</div>
																						</div>

																						<div class="waa-rich-text-wrap">
																								<div>
																										<?php wp_editor( esc_textarea( wpautop( $post->post_content ) ), 'post_content', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content') ); ?>
																								</div>
																						</div>

																						<?php do_action( 'waa_product_edit_after_main' ); ?>

																				</div> <!-- #edit-product -->

																				<div id="product-options">

																						<?php waa_get_template_part( 'edit/options' ); ?>
																						<?php do_action( 'waa_product_edit_after_options' ); ?>

																				</div> <!-- #product-options -->

																				<div id="product-inventory">

																						<?php waa_get_template_part( 'edit/inventory' ); ?>
																						<?php do_action( 'waa_product_edit_after_inventory' ); ?>

																				</div> <!-- #product-inventory -->

																				<div id="product-shipping">

																						<?php waa_get_template_part( 'edit/shipping' ); ?>
																						<?php do_action( 'waa_product_edit_after_shipping' ); ?>

																				</div>

																				<!-- ===== Attributes ===== -->

																				<div class="show_if_simple" id="product-attributes">

																						<?php
																								waa_get_template_part( 'edit/attributes' );
																								waa_get_template_part( 'edit/templates-js' );
																						?>

																						<?php do_action( 'waa_product_edit_after_attributes' ); ?>

																				</div> <!-- #product-attributes -->

																				<div class="show_if_variable" id="product-variations">

																						<?php waa_variable_product_type_options(); ?>

																						<?php do_action( 'waa_product_edit_after_variations' ); ?>

																				</div> <!-- #product-variations -->

																				<?php do_action( 'waa_product_tab_content', $post, $seller_id ); ?>

																		</div> <!-- .tab-content -->
																</div> <!-- .tabbable -->

														</div> <!-- .col-md-7 -->

														<!-- #################### Sidebar ######################### -->

														<div class="waa-product-edit-right waa-edit-sidebar">

																<?php waa_get_template_part( 'edit/sidebar' ); ?>

														</div> <!-- .waa-edit-sidebar -->
												</div> <!-- .product-edit-container -->
										</form>
								</div> <!-- .row -->
						</div> <!-- #primary .content-area -->
				</div><!-- .waa-dashboard-wrap -->
			</div>
		</div>
	</div>
</section>

<script>
    (function($){
        $(document).ready(function(){
            $('#tab-container').easytabs();
            $('#tab-container').bind('easytabs:before', function(){
                $('select.product_tags').chosen();
                $('#product_tag_chosen').css({ width: '100%' });
            });
        });
    })(jQuery)
</script>

<?php
wp_reset_postdata();

if ( ! $from_shortcode ) {
    get_footer();
}
?>