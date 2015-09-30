<?php
/**
 * The Template for displaying products in a product category. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/woocommerce/taxonomy-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

<?php
	/**
	 * woocommerce_before_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	 
	 // breadcrumb
#	do_action( 'woocommerce_before_main_content' );
?>
<?php 

// get category
$term = get_queried_object(); 
$children = get_terms( $term->taxonomy, array(
	'parent'    => $term->term_id,
	'hide_empty' => false
) );

// show artists
if($children) { 
?>
	<div id="content">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				slider<br><br>
					<?php if ( have_posts() ) : ?>
					<?php
						/**
						 * woocommerce_before_shop_loop hook
						 *
						 * @hooked woocommerce_result_count - 20
						 * @hooked woocommerce_catalog_ordering - 30
						 */
						do_action( 'woocommerce_before_shop_loop' );
					?>

					<?php woocommerce_product_loop_start(); ?>
						<?php woocommerce_product_subcategories(); ?>
					<?php woocommerce_product_loop_end(); 
					
					endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php } 
// show artist profile page
else { 
	// get user by category-slug
	$user = get_user_by('login',$term->slug);
	$user_info = get_userdata($user->ID);
	$user_meta = get_user_meta($user->ID);
	#print_r($user_meta);
	?>
	<div id="content">
		<div class="showcase">
			<div class="container">
				<div class="row">
					<div class="artist-banner col-md-12">
						banner
					</div>
				</div>
				<div class="row">				
					<div class="sidebar-right col-md-4 col-sm-6 col-xs-12">
					<?php
					if ( have_posts() ) :
					#do_action( 'woocommerce_before_shop_loop' );
					#woocommerce_product_loop_start();
					?>
					<div class="showcase-content">	
						<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
							<h1 class="page-title"><?= $user_info->display_name; ?></h1>
						<?php endif; ?>
							<?php the_author_image_size(150, 150, $user->ID); ?>
						<div class="showcase2-content">	
							<span class="artist-location"><?= $user_meta['artist_city'][0]; ?>, <?= $user_meta['artist_country'][0]; ?></span><br>
							<span class="artist-art-count">Artworks: <?= $wp_query->found_posts; ?></span>
							<span class="artist-tags">Art Style:
							<?php $artist_tags = wp_get_object_terms( $user->ID, 'style', array('fields' => 'names') );
								$the_tags = rtrim(implode(', ', $artist_tags), ', ');
								echo $the_tags;
							?></span>
							<p><?= $user_meta['description'][0]; ?></p>
						</div>
						<?php if(!empty($user_info->user_url)) { ?>
						<div class="visit-website">
							<a href="<?= $user_info->user_url; ?>" class="btn btn-inverted"> <span> VISIT Website </span></a>
						</div>
						<?php } ?>
						<?php if(!empty($user_meta['facebook'][0]) || !empty($user_meta['twitter'][0]) || !empty($user_meta['googleplus'][0]) || !empty($user_meta['pinterest'][0]) || !empty($user_meta['linkedin'][0])) { ?>
						<div class="content-social-share">
							<span> Share </span>
							<ul>
								<?php if(!empty($user_meta['facebook'][0])) { ?>
								<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?= the_permalink(); ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
								<?php } ?>
								<?php if(!empty($user_meta['twitter'][0])) { ?>
								<li><a href="https://twitter.com/home?status=<?= the_permalink(); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
								<?php } ?>
								<?php if(!empty($user_meta['googleplus'][0])) { ?>
								<li><a href="https://plus.google.com/share?url=<?= the_permalink(); ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li>
								<?php } ?>
								<?php if(!empty($user_meta['pinterest'][0])) { ?>
								<li><a href="https://pinterest.com/pin/create/button/?url=<?= the_permalink(); ?>" target="_blank"><i class="fa fa-pinterest"></i></a></li>
								<?php } ?>
								<?php if(!empty($user_meta['linkedin'][0])) { ?>
								<li><a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=&amp;title=&amp;summary=&amp;source=<?= the_permalink(); ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>						
					</div>
				</div>

				<div class="col-md-8 col-sm-6 col-xs-12">
				<?php
					woocommerce_product_loop_start();
					while ( have_posts() ) : the_post(); ?>
					

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

<?php woocommerce_product_loop_end();	endif; }		?>

		<?php
			/**
			 * woocommerce_after_shop_loop hook
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		?>

<?php
	/**
	 * woocommerce_after_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
?>
</div>
</div>
</div>
</div>
</div>

<?php get_footer( 'shop' ); ?>