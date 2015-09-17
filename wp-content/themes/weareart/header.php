<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( ' | ', true, 'right' ); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <a id="skippy" class="sr-only sr-only-focusable" href="#content"><div class="container"><span class="skiplink-text">Skip to main content</span></div></a>

 <header class="navbar navbar-static-top" id="top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="<?= home_url(); ?>" class="navbar-brand"><img src="<?= get_template_directory_uri(); ?>/img/logo.png" alt="We are Art" /></a>
    </div>
    <nav id="bs-navbar" class="collapse navbar-collapse">
      
			<?php wp_nav_menu( array( 'theme_location' => 'main-menu', 'menu_class' => 'nav navbar-nav' ) ); ?>
    </nav>
  </div>
</header>