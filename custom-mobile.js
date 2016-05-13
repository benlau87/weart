jQuery(document).ready(function($){

    // mobile nav (top)
    $("#nav-mobile").html('<ul>'+$("ul.links").html()+'</ul>');
    $("#nav-trigger span").click(function(e){
        e.stopPropagation();
        if ($("nav#nav-mobile ul").hasClass("expanded")) {
            $("nav#nav-mobile ul.expanded").removeClass("expanded").slideUp(250);
            $(this).removeClass("open");
        } else {
            $("nav#nav-mobile ul").addClass("expanded").slideDown(250);
            $(this).addClass("open");
            $("nav#main-nav-mobile ul.expanded").removeClass("expanded").slideUp(750);
            $("#main-nav-trigger").removeClass("open");
        }
    });

    // mobile nav (main)
    $('#main-nav-mobile').html('<ul>'+$("#nav").html()+'</ul>');
    $('#main-nav-mobile > ul').append('<li>'+$('#nav-mobile ul > li.last').html()+'</li>');
    $('#main-nav-mobile > ul').append('<li>'+$('#nav-mobile ul > li:nth-of-type(2)').html()+'</li>');
    $('#main-nav-mobile > ul').append('<li>'+$('#search_mini_form').parent().html()+'</li>');
    var produkt_link = $('#main-nav-mobile > ul > li.level0.nav-1 > a').wrap('<p/>').parent().html();
    $('#main-nav-mobile > ul > li.level0.nav-1 > p > a').unwrap();
    $('#main-nav-mobile > ul > li.level0.nav-1 > ul').prepend('<li>'+produkt_link+'</li>');
    $('#main-nav-mobile > ul > li.level0.nav-1 > ul > li:first-child > a > span:first-of-type').text('Alle Produkte');
    var vorlagen_link = $('#main-nav-mobile > ul > li.level0.nav-4 > a').wrap('<p/>').parent().html();
    $('#main-nav-mobile > ul > li.level0.nav-4 > p > a').unwrap();
    $('#main-nav-mobile > ul > li.level0.nav-4 > ul').prepend('<li>'+vorlagen_link+'</li>');
    $('#main-nav-mobile > ul > li.level0.nav-4 > ul > li:first-child > a > span:first-of-type').text('Alle Vorlagen');

    $('#home-main-teaser .item, #bestseller-items .item').swipeleft(function() {
        home_teaser_slider.next();
    });

    $('#home-main-teaser .item, #bestseller-items .item').swiperight(function() {
        home_teaser_slider.prev();
    });

    $('#bestseller-items .item').swipeleft(function() {
        bestseller_slider.next();
    });

    $('#bestseller-items .item').swiperight(function() {
        bestseller_slider.prev();
    });

    $("#search_mini_form").find('.button').attr('title', 'Suchen');
    $("#main-nav-mobile li").has("ul").addClass("menu-item-has-children");
    $("#main-nav-mobile li.menu-item-has-children").append('<i class="fa fa-caret-down"></i>');
    $("#main-nav-mobile li.menu-item-has-children > ul > li").append('<i class="fa fa-angle-right"></i>');

    $("#main-nav-mobile > ul > li.menu-item-has-children > a:first-of-type").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).parent().toggleClass("open");
        $(this).parent().siblings().removeClass("open");
        $(this).parent().next("i.fa").removeClass("fa-caret-down").addClass("fa-caret-up");
    });

    $("#main-nav-trigger").click(function(e){
        e.stopPropagation();
        if ($("nav#main-nav-mobile ul").hasClass("expanded")) {
            $("nav#main-nav-mobile ul.expanded").removeClass("expanded").slideUp(750);
            $(this).removeClass("open");
            $('#search_mini_form').hide();
        } else {
            $("nav#main-nav-mobile > ul").addClass("expanded").slideDown(250);
            $(this).addClass("open");
            $('#search_mini_form').show();
            $("nav#nav-mobile ul.expanded").removeClass("expanded").slideUp(250);
            $("#nav-trigger span").removeClass("open");
        }
    });

    // close nav on page click
    $(document).on('click',function(){
        $("nav#main-nav-mobile ul.expanded, nav#nav-mobile ul.expanded").removeClass("expanded").slideUp(250);
        $("#main-nav-trigger").removeClass("open");
    });

    $('#main-nav-mobile li a, #nav-mobile ul li a').not('li.menu-item-has-children > a, li.last > a, input').click(function(e) {
        e.stopPropagation();
        $('#loading').show();
        $("nav#main-nav-mobile ul.expanded, nav#nav-mobile ul.expanded").removeClass("expanded").slideUp(750);
        $("#main-nav-trigger").removeClass("open");
    });

    $("#main-nav-mobile #search_mini_form .button span").html('Suchen');

    // fix sidebar layout for tablets and smartphones
    if($(window).width() < 1100) {
        $('.sidebar .block-teaser > p > a > img').each(function () {
            //if($(this).parent().parent().is('p')) {
                var height = $(this).height();
                $(this).parent().parent().height(height);
           // }
        });
    }


    // mobile search
    $('#mobile-cart span').click(function() {
        $('#mobile-cart').toggleClass("open");
        $('#mobile-cart').append($('#cart-box'));

        if($(window).width() < 460) {
            $('.header .quick-access #notice').toggle();
        }
    });

    if($(window).width() < 1010 && $(window).width() > 640) {
        $('.products-grid > li .product-name a span').each(function() {
            if ($(this).text().length > 25)
                $(this).text($(this).text().substring(0,25) + '...');
        });
    }
    /*
     $(window).load(function(){

     $('#content ul.products-grid').each(function(){

     var highestBox = 0;
     $('.product-box', this).each(function(){

     if($(this).innerHeight() > highestBox)
     highestBox = $(this).innerHeight();
     });

     $('.item',this).innerHeight(highestBox);
     alert(highestBox);
     });
     });
     */

    /*
     if($(window).width() < 380) {		*/
    $('#side_teaser li a img').height($('#teaser_slider li').height());

    $(window).resize(function() {
        $('#side_teaser li a img').height($('#teaser_slider li').height());
    });
    /*} */
});