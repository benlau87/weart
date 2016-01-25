<div class="container">
	<div class="row">
		<footer id="footer" role="contentinfo">
				<?php dynamic_sidebar('footer-col-1'); ?>
				<?php dynamic_sidebar('footer-col-2'); ?>
				<?php dynamic_sidebar('footer-col-3'); ?>
				<?php dynamic_sidebar('footer-col-4'); ?>
			<div class="clearfix"></div>
			<div id="copyright">
				<div class="col-md-8">
					<?php echo sprintf( __( '%1$s %2$s %3$s. All Rights Reserved.', 'waa' ), '&copy;', date( 'Y' ), esc_html( get_bloginfo( 'name' ) ) ); ?>
				</div>
				<div class="col-md-4 text-right socials">
					<a href="https://www.facebook.com/weartlovers/" target="_blank"><i class="ui ui-facebook"></i></a>
					<a href="https://www.instagram.com/weartlovers/" target="_blank"><i class="ui ui-instagram"></i></a>
				</div>
			</div>
		</footer>
	</div>
</div>
<?php wp_footer(); ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-69255488-1']);
  _gaq.push(['_gat._anonymizeIp']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>