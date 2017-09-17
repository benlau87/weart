<?php get_header(); ?>
<section id="content" role="main">
	<div class="blog-grid">
		<div class="container">
			<div class="row">
				<div class="sidebar-left col-md-4 col-sm-6 col-xs-12">
					<div class="showcase-content">	
						<h1 class="page-title">Blog & Events</h1>
						<div class="showcase2-content">	
							<p>Hier findest du aktuelle Beiträge und Events.</p>
						</div>
						<!--<div class="visit-website">
							<a href="" class="btn btn-inverted"> <span> Subscribe </span></a>
						</div>-->
					</div>
				</div>
				<div class="col-md-8 masonry-grid">
					<div class="grid-sizer-masonry"></div>
					<div class="gutter-sizer-masonry"></div>
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'entry' ); ?>
					<?php comments_template(); ?>
					<?php endwhile; endif; ?>
					<?php get_template_part( 'nav', 'below' ); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>