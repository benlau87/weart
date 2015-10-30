// jQuery
 jQuery(document).ready(function($){
 
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

		$('#artists-page li.product.type-product').each(function() {
			var height = $(this).height();
			if(height < 175) {
					$(this).find('.bottom').hide();
					$('.entry_author_image', this).css('width', '75px');
					$('.entry_author_image', this).css('height', '75px');
				}
		});		
	
});