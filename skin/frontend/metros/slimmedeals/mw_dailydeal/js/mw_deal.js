jQuery( window ).load(function() {
    jQuery('.more-info').click(function(){
        var parent = jQuery(this).parent().parent().parent().parent();
        var ele = parent.find('.attribute-specs');
        ele.toggle();

        var top = ele.position().top;
        jQuery(window).scrollTop(top);

        console.log (jQuery(this).parent().parent().parent().parent());
    });
});