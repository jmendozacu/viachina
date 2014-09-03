



jQuery(function($) {

    $('.ajx-cart').live('click', function () {
        if ( $(window).width() < 769 )  {
            return false;
        }
        var cart = $('.shopping_bg');
        var imgtodrag = $(this).parents('li.item').find('a.product-image img:not(.back_img)').eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                .offset({ top:imgtodrag.offset().top, left:imgtodrag.offset().left })
                .css({'opacity':'0.7', 'position':'absolute', 'height':'150px', 'width':'150px', 'z-index':'1000'})
                .appendTo($('body'))
                .animate({
                    'top':cart.offset().top + 10,
                    'left':cart.offset().left + 30,
                    'width':55,
                    'height':55
                }, 1000, 'easeInOutExpo');
            imgclone.animate({'width':0, 'height':0}, function(){ $(this).detach() });
        }
        return false;
    });


  

});
