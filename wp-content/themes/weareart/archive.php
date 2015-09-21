<?php get_header(); ?>
<section id="content" role="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<header class="header">
				<h1 class="entry-title"><?php 
				if ( is_day() ) { printf( __( 'Daily Archives: %s', 'waa' ), get_the_time( get_option( 'date_format' ) ) ); }
				elseif ( is_month() ) { printf( __( 'Monthly Archives: %s', 'waa' ), get_the_time( 'F Y' ) ); }
				elseif ( is_year() ) { printf( __( 'Yearly Archives: %s', 'waa' ), get_the_time( 'Y' ) ); }
				else { _e( 'Archives', 'waa' ); }
				?></h1>
				</header>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'entry' ); ?>
				<?php endwhile; endif; ?>
				<?php get_template_part( 'nav', 'below' ); ?>
			</div>
		</div>
	</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>