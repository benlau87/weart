// jQuery
 jQuery(document).ready(function($){
 
		// masonry-grid
    var $container = $('ul.products');   
    $container.imagesLoaded( function(){
      $container.masonry({
        itemSelector : 'li.product',
				columnWidth: '.grid-sizer',
				gutter: '.gutter-sizer'
      });
    });
		
		var $masonry_container = $('.masonry-grid')   
    $masonry_container.imagesLoaded( function(){
      $masonry_container.masonry({
        itemSelector : 'article.type-post',
				columnWidth: '.grid-sizer-masonry',
				gutter: '.gutter-sizer-masonry'
      });
    });
		
		$("ul.products li.product").click(function() {
			window.location = $(this).find("a").attr("href"); 
			return false;
		});
		$("ul.products li.product a").click(function(e){
			e.stopPropagation();
			//show tooltip
		});

		// resize avatar picture, if art image to small
		/* $('#artists-page li.product.type-product').each(function() {
			if($(this).outerHeight() < 175) {
					$(this).find('.bottom').hide();
					$('.entry_author_image', this).css('width', '75px');
					$('.entry_author_image', this).css('height', '75px');
				}
		}); */
		
		$('.woocommerce .shop_table dd.variation-Gre p, .woocommerce .shop_table dd.variation-Material p').each(function() {
			if($(this).text() == 'Original') {
				$(this).parent().hide();	
				$(this).parent().prev('dt').hide();
			}
		});
	
			
		if(window.chrome) {
				$('.art-slider li').css('background-size', '100% 100%');
			}
			var unslider = $('.art-slider').unslider({
				fluid: true,
				dots: true,
				keys: true,
				delay: 7000
			});
    
	
	
});