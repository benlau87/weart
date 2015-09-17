<?php get_header(); ?>
<section id="content" role="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12" role="main">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<section class="entry-content">
						<?php if ( has_post_thumbnail() ) { ?>
						<div class="hero-img">
							<?php the_post_thumbnail('full', array('class'=>'img-responsive')); ?>
							<div class="hero-caption">
							<?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?>
							</div>
						</div>
						<?php } ?>
						<?php the_content(); ?>
					</section>
				</article>
				<?php endwhile; endif; ?>
			</div>
		</div>
	</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>