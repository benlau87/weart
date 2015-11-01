<?php get_header(); ?>
<section id="content" role="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12" role="main">
				<?php 
				if ( is_singular( 'product' ) ) {
					 woocommerce_content();
				}else{
				 //For ANY product archive.
				 //Product taxonomy, product search or /shop landing
					woocommerce_get_template( 'archive-product.php' );
				}
				?>
			</div>
		</div>
	</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>