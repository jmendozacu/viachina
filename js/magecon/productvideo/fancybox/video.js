/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_ProductVideo
 * @version    2.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */

var $pvideo = jQuery.noConflict();
$pvideo(document).ready(function() {
    $pvideo(".pvideo").click(function() {
        $pvideo.fancybox({
            'padding'		 	: 0,
            'showNavArrows'  	        : false,
            'autoScale'		 	: false,
            'transitionIn'	 	: videoProductTransition,
            'transitionOut'	 	: videoProductTransition,
            'speedIn'			: videoProductTransitionSpeed,
            'speedOut'			: videoProductTransitionSpeed,
            'changeSpeed'		: videoProductTransitionSpeed,
            'titlePosition'             : videoProductTitlePosition,
	    'showCloseButton'           : videoProductShowClose,
            'enableEscapeButton'        : videoProductEnableESC,
	    'title'			: this.title,
            'width'			: videoProductWidth,
            'height'		 	: videoProductHeight,
            'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
            'type'			: 'swf',
            'swf'			: {
            'wmode'			: 'transparent',
            'allowfullscreen'	        : 'true'
            }
        });
        return false;
    });
	
    $pvideo("a.pimages").fancybox({
        'showNavArrows' 	: videoProductImagesNavArrow,
        'cyclic'		: videoProductImagesCyclic,
	'transitionIn'	 	: videoProductTransition,
        'transitionOut'	 	: videoProductTransition,
	'speedIn'		: videoProductTransitionSpeed,
        'speedOut'		: videoProductTransitionSpeed,
        'changeSpeed'		: videoProductTransitionSpeed,
        'titlePosition'  	: videoProductTitlePosition,
	'showCloseButton'	: videoProductShowClose,
        'enableEscapeButton'    : videoProductEnableESC
    });
});

