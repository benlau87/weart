<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header>
	<?php if ( has_post_thumbnail() ) { ?><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_post_thumbnail(); ?></a><?php } ?>
<?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="entry-title">'; } ?><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a><?php if ( is_singular() ) { echo '</h1><hr style="width:25px; margin: 25px auto 10px">'; } else { echo '</h2>'; } ?> <?php edit_post_link(); ?>
</header>
<?php get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
<?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
<?php if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
</article>