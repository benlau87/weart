<?php get_header(); ?>
    <section id="content" role="main">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <header class="header">
                        <h1 class="entry-title"><?php _e('Seite nicht gefunden', 'waa'); ?></h1>
                    </header>
                    <section class="entry-content">
                        <p><?php _e('Leider konnten wir die angeforderte Seite nicht finden. Stattdessen suchen?', 'waa'); ?></p>
                        <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    </section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>