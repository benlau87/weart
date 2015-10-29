<?php
/**
 *
 * @package waa
 * @package waa - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$store_user   = get_userdata( get_query_var( 'author' ) );
$store_info   = waa_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
$scheme       = is_ssl() ? 'https' : 'http';

wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?sensor=true' );

get_header( 'shop' );
?>
<div id="content">
			<div class="showcase woocommerce">
				<div class="container">
					<div class="row">
				<div id="waa-primary" class="waa-single-store col-md-12">
						<div id="waa-content" class="store-page-wrap woocommerce" role="main">

								<?php waa_get_template_part( 'store-header' ); ?>

								<?php do_action( 'waa_store_profile_frame_after', $store_user, $store_info ); ?>
								
								<div class="row">
									<div class="sidebar-right col-md-4 col-sm-6 col-xs-12">
										<div class="showcase-content">	
										<?php if ( isset( $store_info['store_name'] ) ) { ?>
											<h1 class="page-title"><?php echo esc_html( $store_info['store_name'] ); ?></h1>
										<?php } ?>
													
										<div class="entry_author_image"><?php echo get_avatar( $store_user->ID, 150 ); ?></div>
											<div class="showcase2-content">	
												<?php if ( isset( $store_info['address']['city'] ) && !empty( $store_info['address']['city'] ) ) { ?>
												<span class="artist-location"><?= $store_info['address']['city'];
													if ( isset( $store_info['address']['country'] ) && !empty( $store_info['address']['country'] ) ) { echo ', '.$store_info['address']['country'];  }?></span><br>
												<?php } ?>
												<span class="artist-art-count"><?= __('Artworks', 'waa'); ?>: <?= waa_count_posts('product', $store_user->ID)->total ?></span>
												<?php if ( isset( $store_info['description'] ) && !empty( $store_info['description'] ) ) { ?>
														<p class="artist-description"><?= $store_info['description']; ?></p>
												<?php } ?>
											</div>
											<?php if ( isset( $store_info['website'] ) && !empty( $store_info['website'] ) ) { ?>
											<div class="visit-website">
												<a href="<?= $store_info['website'] ?>" class="btn btn-inverted"> <span> <?= __('VISIT Website', 'waa'); ?> </span></a>
											</div>
											<?php }
											if ( is_array ( $store_info['social'] ) && !empty( $store_info['social'] ) ) : ?>
											<div class="content-social-share">
												<span> Share </span>
												<ul>
													<?php 	if ( isset ( $store_info['social']['fb'] ) && !empty ( $store_info['social']['fb'] ) ) { ?><li><a href="<?= $store_info['social']['fb'] ?>" target="_blank"><i class="fa fa-facebook"></i></a></li><?php } ?>
													<?php 	if ( isset ( $store_info['social']['twitter'] ) && !empty ( $store_info['social']['twitter'] ) ) { ?><li><a href="<?= $store_info['social']['twitter'] ?>" target="_blank"><i class="fa fa-twitter"></i></a></li><?php } ?>
													<?php 	if ( isset ( $store_info['social']['gplus'] ) && !empty ( $store_info['social']['gplus'] ) ) { ?><li><a href="<?= $store_info['social']['gplus'] ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li><?php } ?>
													<?php 	if ( isset ( $store_info['social']['instagram'] ) && !empty ( $store_info['social']['instagram'] ) ) { ?><li><a href=<?= $store_info['social']['instagram'] ?>"" target="_blank"><i class="fa fa-instagram"></i></a></li><?php } ?>
													<?php 	if ( isset ( $store_info['social']['youtube'] ) && !empty ( $store_info['social']['youtube'] ) ) { ?><li><a href="<?= $store_info['social']['youtube'] ?>" target="_blank"><i class="fa fa-youtube"></i></a></li><?php } ?>
													<?php 	if ( isset ( $store_info['social']['flickr'] ) && !empty ( $store_info['social']['flickr'] ) ) { ?><li><a href="<?= $store_info['social']['flickr'] ?>" target="_blank"><i class="fa fa-flickr"></i></a></li><?php } ?>
													<?php 	if ( isset ( $store_info['social']['linkedin'] ) && !empty ( $store_info['social']['linkedin'] ) ) { ?><li><a href="<?= $store_info['social']['linkedin'] ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li><?php } ?>
												</ul>
											</div>
											<?php endif; ?>	
									</div>
								</div>										
				

								<?php if ( have_posts() ) { ?>

										<div class="col-md-8 col-sm-6 col-xs-12">

												<?php woocommerce_product_loop_start(); ?>

														<?php while ( have_posts() ) : the_post(); ?>

																<?php wc_get_template_part( 'content', 'product' ); ?>

														<?php endwhile; // end of the loop. ?>

												<?php woocommerce_product_loop_end(); ?>

										</div>

										<?php waa_content_nav( 'nav-below' ); ?>

								<?php } else { ?>

										<p class="waa-info"><?php _e( 'No products were found of this seller!', 'waa' ); ?></p>

								<?php } ?>
						</div>

				</div><!-- .waa-single-store -->

				<?php do_action( 'woocommerce_after_main_content' ); ?>
			</div>
		</div>
	</div>
	</diiv>
</div>

<?php get_footer( 'shop' ); ?>