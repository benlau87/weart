<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<title><?php bloginfo('name'); ?></title>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
<?php wp_head(); ?>
  <script type="text/javascript">
    var waa_url = '<?= get_bloginfo("template_url"); ?>';
  </script>
</head>
<body <?php body_class(); ?>>
  <a id="skippy" class="sr-only sr-only-focusable" tabindex="-1" href="#content"><div class="container"><span class="skiplink-text">Skip to main content</span></div></a>

<header class="navbar navbar-static-top
<?php if(is_home()) {
	$img = wp_get_attachment_image_src(get_post_thumbnail_id(get_option('page_for_posts')),'full'); 
  $featured_image = $img[0];
	echo ' header-image" style="background-image:url(' . $featured_image . ')"';
	} else { echo '"'; }
?> id="top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar top-bar"></span>
        <span class="icon-bar middle-bar"></span>
        <span class="icon-bar bottom-bar"></span>
      </button>
      <a href="<?= home_url(); ?>" class="navbar-brand"><img src="<?= get_template_directory_uri(); ?>/img/logo.png" alt="We are Art" /></a>
    </div>
    <nav id="bs-navbar" class="collapse navbar-collapse">      
			<?php 	wp_nav_menu( array( 'theme_location' => 'main-menu', 'menu_class' => 'nav navbar-nav' ) ); ?>
    </nav>		
			<?php if( is_home() ) { ?>
				<div class="hero-banner">
					<h1>WeArt&nbsp;&nbsp;Blog</h2>
				</div>
			<?php } ?>
  </div>
</header>