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
$term = get_queried_object(); 
$children = get_terms( $term->taxonomy, array(
'parent'    => $term->term_id,
'hide_empty' => false
) );
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
<?php } else { ?>
	<div id="content">
		<div class="showcase">
			<div class="container">
				<div class="row">
					<div class="sidebar-right col-md-4 col-sm-4 col-xs-12">
					<?php
					if ( have_posts() ) :
					#do_action( 'woocommerce_before_shop_loop' );
					woocommerce_product_loop_start();
					?>
					<div class="showcase-content">	
						<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
							<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
						<?php endif; ?>
						<div class="showcase2-content">						
							<p>
							<?php
							/**
							 * woocommerce_archive_description hook
							 *
							 * @hooked woocommerce_taxonomy_archive_description - 10
							 * @hooked woocommerce_product_archive_description - 10
							 */
							do_action( 'woocommerce_archive_description' );
							?>
						</p>
						</div>
						<div class="visit-website">
							<a href="#" class="btn btn-inverted"> <span> VISIT Website </span></a>
						</div>
						<div class="content-social-share">
							<span> Share </span>
							<ul>
								<li><a class="style1" href="https://www.facebook.com/sharer/sharer.php?u=http://mo.themestudio.net/portfolios/portfolio-2/" target="_blank"><i class="fa fa-facebook"></i></a></li>
								<li><a class="style2" href="https://twitter.com/home?status=http://mo.themestudio.net/portfolios/portfolio-2/" target="_blank"><i class="fa fa-twitter"></i></a></li>
								<li><a class="style3" href="https://plus.google.com/share?url=http://mo.themestudio.net/portfolios/portfolio-2/" target="_blank"><i class="fa fa-google-plus"></i></a></li>
								<li><a class="style4" href="https://pinterest.com/pin/create/button/?url=http://mo.themestudio.net/portfolios/portfolio-2/&amp;media=&amp;" target="_blank"><i class="fa fa-pinterest"></i></a></li>
								<li><a class="style6" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=&amp;title=&amp;summary=&amp;source=http://mo.themestudio.net/portfolios/portfolio-2/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
							</ul>
						</div>                    				
					</div>
				</div>

				<div class="col-md-8 col-sm-8 col-xs-12">
				<?php
					while ( have_posts() ) : the_post(); ?>
					
			<?php woocommerce_product_loop_start(); ?>
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
