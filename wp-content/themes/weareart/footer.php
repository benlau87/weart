<div class="container">
	<div class="row">
		<footer id="footer" role="contentinfo">
			<div class="row">
				<?php dynamic_sidebar('footer-col-1'); ?>
				<?php dynamic_sidebar('footer-col-2'); ?>
				<?php dynamic_sidebar('footer-col-3'); ?>
				<?php dynamic_sidebar('footer-col-4'); ?>
			</div>
			<div class="row" id="copyright">
			<div class="col-md-12">
				<?php echo sprintf( __( '%1$s %2$s %3$s. All Rights Reserved.', 'waa' ), '&copy;', date( 'Y' ), esc_html( get_bloginfo( 'name' ) ) ); ?>
			</div>
			</div>
		</footer>
	</div>
</div>
<?php wp_footer(); ?>
</body>
</html>