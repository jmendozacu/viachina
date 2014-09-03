jQuery( window ).load(function() {
    jQuery('.more-info').click(function(){
        var parent = jQuery(this).parent().parent().parent().parent();
        parent.find('.attribute-specs').toggle();
        console.log (jQuery(this).parent().parent().parent().parent());
    });
});