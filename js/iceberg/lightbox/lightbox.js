// -----------------------------------------------------------------------------------
//  Icbeberg Commerce 2010
//  Lightbox Version 1.0.0
//
//  This library is based on Lightbox v2.05 by Lokesh Dhakar
//  It has only been slightly modified 
//  - functions as a generic modal window
//  - It can now pull ajax content and cache it
//  - it has inline prev/next buttons on the bottom nav bar
//  - slideshow is circular, once last image is reached, it goes to the first one
//
//  ----------------------------------------------------------------------------------
//
//	Lightbox v2.05
//	by Lokesh Dhakar - http://www.lokeshdhakar.com
//
//	For more information, visit:
//	http://lokeshdhakar.com/projects/lightbox2/
//
//	Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
//  	- Free for use in both personal and commercial projects
//		- Attribution requires leaving author name, author link, and the license info intact.
//	
//  Thanks: Scott Upton(uptonic.com), Peter-Paul Koch(quirksmode.com), and Thomas Fuchs(mir.aculo.us) for ideas, libs, and snippets.
//  		Artemy Tregubenko (arty.name) for cleanup and help in updating to latest ver of proto-aculous.
//
// -----------------------------------------------------------------------------------

//  Configuration
LightboxOptions = Object.extend({
    overlayOpacity: 0.8,   // controls transparency of shadow overlay

    animate: true,         // toggles resizing animations
    resizeSpeed: 10,        // controls the speed of the image resizing animations (1=slowest and 10=fastest)

    borderSize: 10,         //if you adjust the padding in the CSS, you will need to update this variable
}, window.LightboxOptions || {});

// -----------------------------------------------------------------------------------

var IBLightbox = Class.create();

IBLightbox.prototype = {
    imageArray: [],
    activeImage: undefined,
    
    // initialize()
    // Constructor runs on completion of the DOM loading. Calls updateImageList and then
    // the function inserts html at the bottom of the page which is used to display the shadow 
    // overlay and the image container.
    //
    initialize: function() {    
        
        this.modalCache = new Array();
        this.updateImageList();
        
        this.keyboardAction = this.keyboardAction.bindAsEventListener(this);

        if (LightboxOptions.resizeSpeed > 10) LightboxOptions.resizeSpeed = 10;
        if (LightboxOptions.resizeSpeed < 1)  LightboxOptions.resizeSpeed = 1;

	    this.resizeDuration = LightboxOptions.animate ? ((11 - LightboxOptions.resizeSpeed) * 0.15) : 0;
	    this.overlayDuration = LightboxOptions.animate ? 0.2 : 0;  // shadow fade in/out duration

        // When Lightbox starts it will resize itself from 250 by 250 to the current image dimension.
        // If animations are turned off, it will be hidden as to prevent a flicker of a
        // white 250 by 250 box.
        var size = (LightboxOptions.animate ? 450 : 1) + 'px';
        

        // Code inserts html at the bottom of the page that looks similar to this:
        //
        //  <div id="iboverlay"></div>
        //  <div id="iblightbox">
        //      <div id="outerImageContainer">
        //          <div id="imageContainer">
        //              <img id="lightboxImage">
        //              <div id="iblightboxHTML"></div>
        //              <div style="" id="hoverNav">
        //                  <a href="#" id="prevLink"></a>
        //                  <a href="#" id="nextLink"></a>
        //              </div>
        //              <div id="loading">
        //                  <a href="#" id="loadingLink">
        //                      <img src="images/loading.gif">
        //                  </a>
        //              </div>
        //          </div>
        //      </div>
        //      <div id="imageDataContainer">
        //          <div id="imageData">
        //              <div id="imageDetails">
        //                  <span id="caption"></span>
        //                  <span id="numberDisplay"></span>
        //              	<div style="" id="staticNav">
        //                 	 	<a href="#" id="prevStaticLink"></a>
        //                 	 	<a href="#" id="nextStaticLink"></a>
        //              	</div>
        //              </div>
        //              <div id="bottomNav">
        //                  <a href="#" id="bottomNavClose">
        //                      <img src="images/close.gif">
        //                  </a>
        //              </div>
        //          </div>
        //      </div>
        //  </div>


        if (!$('iboverlay') && !$('iblightbox'))
        {
	        var objBody = $$('body')[0];
	
			objBody.appendChild(Builder.node('div',{id:'iboverlay'}));
		
	        objBody.appendChild(Builder.node('div',{id:'iblightbox'}, [
	            Builder.node('div',{id:'ibouterImageContainer'}, 
	                Builder.node('div',{id:'ibimageContainer'}, [
	                    Builder.node('img',{id:'iblightboxImage'}), 
	                    Builder.node('div',{id:'iblightboxHTML'}), 
	                    Builder.node('div',{id:'ibhoverNav'}, [
	                        Builder.node('a',{id:'ibprevLink', href: '#' }),
	                        Builder.node('a',{id:'ibnextLink', href: '#' })
	                    ]),
	                    Builder.node('div',{id:'ibloading'}, 
	                        Builder.node('a',{id:'ibloadingLink', href: '#' }, 
	                            Builder.node('span', 'Loading...')
	                        )
	                    )
	                ])
	            ),
	            Builder.node('div', {id:'ibimageDataContainer'},
	                Builder.node('div',{id:'ibimageData'}, [
	                    Builder.node('div',{id:'ibimageDetails'}, [
	                        Builder.node('span',{id:'ibcaption'}),
	                        Builder.node('span',{id:'ibnumberDisplay'}),
	                        Builder.node('div',{id:'ibstaticNav'}, [
		                        Builder.node('a',{id:'ibprevStaticLink', href: '#' }, [
		                        	Builder.node('span',{},"Prev")
		                        ]),
		                        Builder.node('a',{id:'ibnextStaticLink', href: '#' }, [
		                        	Builder.node('span',{},"Next")
		                        ]),
		                    ]),
	                    ]),
	                    Builder.node('div',{id:'ibbottomNav'},
	                        Builder.node('a',{id:'ibbottomNavClose', href: '#' },
	                        	Builder.node('span', 'Close Window')
	                        )
	                    )
	                ])
	            )
	        ]));
        }

        
		$('iboverlay').hide().observe('click', (function() { this.end(); }).bind(this));
		$('iblightbox').hide().observe('click', (function(event) { if (event.element().id == 'iblightbox') this.end(); }).bind(this));
		$('ibouterImageContainer').setStyle({ width: size, height: size });
		
		$('ibprevLink').observe('click', (function(event) { event.stop(); this.changeImage(this.activeImage - 1); }).bindAsEventListener(this));
		$('ibnextLink').observe('click', (function(event) { event.stop(); this.changeImage(this.activeImage + 1); }).bindAsEventListener(this));
		
		$('ibprevStaticLink').observe('click', (function(event) { event.stop(); this.changeImage(this.activeImage - 1); }).bindAsEventListener(this));
		$('ibnextStaticLink').observe('click', (function(event) { event.stop(); this.changeImage(this.activeImage + 1); }).bindAsEventListener(this));
		
		
		$('ibloadingLink').observe('click', (function(event) { event.stop(); this.end(); }).bind(this));
		$('ibbottomNavClose').observe('click', (function(event) { event.stop(); this.end(); }).bind(this));

		
        var th = this;
        (function(){
            var ids = 
                'overlay lightbox outerImageContainer imageContainer lightboxImage lightboxHTML hoverNav prevLink nextLink loading loadingLink ' + 
                'imageDataContainer imageData imageDetails caption numberDisplay bottomNav bottomNavClose staticNav prevStaticLink nextStaticLink';   
            $w(ids).each(function(id){ th[id] = $('ib'+id); });
        }).defer();
        
    },

    //
    // updateImageList()
    // Loops through anchor tags looking for 'lightbox' references and applies onclick
    // events to appropriate links. You can rerun after dynamically adding images w/ajax.
    //
    updateImageList: function() {   
        this.updateImageList = Prototype.emptyFunction;

        document.observe('click', (function(event){
            var target = event.findElement('a[rel^=iblightbox]') || event.findElement('area[rel^=iblightbox]');
            if (target) {
                event.stop();
                this.start(target);
            }
        }).bind(this));
    },
    
    parseAdvancedMediaCofig: function(element)
    {
    	var advancedConfig = new Object();
    	
    	if (element.getAttribute('modal'))
    	{
    		advancedConfig.isModal  = true;
    		var x = element.getAttribute('modal').split(";");
    		
    		for (var i=0; i<x.length; ++i)
    		{
    			if (x[i].indexOf("width")!=-1)
    			{
    				var y = x[i].split("=");
    				advancedConfig.width = y[1] * 1.0;
    			}
    			
    			if (x[i].indexOf("height")!=-1)
    			{
    				var y = x[i].split("=");
    				advancedConfig.height = y[1] * 1.0;
    			}
    		}
    	}
    	
    	if (element.getAttribute("rel").indexOf("modal")!=-1)
    	{
    		advancedConfig.isModal  = element.getAttribute("rel").indexOf("modal")!=-1;
    		var x = element.getAttribute('rel').split(";");
    		
    		for (var i=0; i<x.length; ++i)
    		{
    			if (x[i].indexOf("width")!=-1)
    			{
    				var y = x[i].split("=");
    				advancedConfig.width = y[1] * 1.0;
    			}
    			
    			if (x[i].indexOf("height")!=-1)
    			{
    				var y = x[i].split("=");
    				advancedConfig.height = y[1] * 1.0;
    			}
    		}
    	}
    	return advancedConfig;
    },
    
    //
    //  start()
    //  Display overlay and lightbox. If image is part of a set, add siblings to imageArray.
    //
    start: function(imageLink) {    

    	if (imageLink.getAttribute("rel").indexOf("iblightbox")==-1)
    	{
    		return false;
    	}
    	
        $$('select', 'object', 'embed').each(function(node){ node.style.visibility = 'hidden' });

        // stretch overlay to fill page and fade in
        var arrayPageSize = this.getPageSize();
        $('iboverlay').setStyle({ width: arrayPageSize[0] + 'px', height: arrayPageSize[1] + 'px' });

        new Effect.Appear(this.overlay, { duration: this.overlayDuration, from: 0.0, to: LightboxOptions.overlayOpacity });

        this.imageArray = [];
        var imageNum = 0;    
        
        var advancedMediaConfig = this.parseAdvancedMediaCofig(imageLink);

        if ((imageLink.getAttribute("rel").split(";") == 'iblightbox'))
        {
        	if (advancedMediaConfig.isModal)
        	{
        		this.imageArray.push([imageLink.href, imageLink.title, true, advancedMediaConfig]);
        	}
        	else
        	{
        		// Its an Image
            	this.imageArray.push([imageLink.href, imageLink.title, false, null]);
        	}
        } 
        else 
        {
            // Part of a set
        	this.imageArray = 
                $$(imageLink.tagName + '[href][rel="' + imageLink.rel + '"]').
                collect(function(anchor){ return [anchor.href, anchor.title, anchor.getAttribute('modal') ? true : false, advancedMediaConfig]; }).
                uniq();
            
            while (this.imageArray[imageNum][0] != imageLink.href) { imageNum++; }
        }

        // Calculate top and left offset for the lightbox 
        var arrayPageScroll = document.viewport.getScrollOffsets();
        var lightboxTop = arrayPageScroll[1] + (document.viewport.getHeight() / 10);
        var lightboxLeft = arrayPageScroll[0];
        this.lightbox.setStyle({ top: lightboxTop + 'px', left: lightboxLeft + 'px' }).show();
        
        this.changeImage(imageNum);
    },

    processInfo: function(response)
    {
    	this.modalCache[this.currentLink] = response.responseText;
		this.updateModal(response.responseText);	
	},
	
	updateModal: function(html)
	{
		this.loading.hide();
		this.lightboxHTML.innerHTML = html;
		this.lightboxImage.hide();
        this.lightboxHTML.show();
       
		var firstChild = null;
		var children = $('iblightboxHTML').children;
		for (var i in children)
		{
			if (children[i].nodeType == 1)
			{
				firstChild = children[i];
				break;
			}
		}
	 
		if (firstChild)
		{
	    	var newWidth  = firstChild.offsetWidth;
	    	var newHeight = firstChild.offsetHeight + 20; 
	    	this.resizeImageContainer(newWidth, newHeight);
	   		this.imageDataContainer.setStyle({ width: newWidth+20 + 'px' });
		}

        this.updateDetails();
        this.hoverNav.hide();
        this.staticNav.hide();
        this.disableKeyboardNav();
        //this.caption.hide();
	},
	
	
    //
    //  changeImage()
    //  Hide most elements and preload image in preparation for resizing image container.
    //
    changeImage: function(imageNum) {  

    	this.lightboxHTML.innerHTML = "";
    	
       	if (this.imageArray.length <= 1)
        {
        	this.staticNav.hide();   
        }
        
    	if (imageNum < 0 || imageNum >= this.imageArray.length)
    	{
    		// Have to do a modulus op
    		if (imageNum < 0)
    		{
    			imageNum = this.imageArray.length - ((imageNum*-1) % this.imageArray.length);
    		}
    		else
    		{
    			imageNum = imageNum % this.imageArray.length;
    		}
    	}

    	this.activeImage = imageNum; // update global var

        // hide elements during transition
        if (LightboxOptions.animate) 
        {
        	this.loading.show();
        }
        
        this.lightboxImage.hide();
        this.lightboxHTML.hide();
        this.hoverNav.hide();
        this.prevLink.hide();
        this.nextLink.hide();
		// HACK: Opera9 does not currently support scriptaculous opacity and appear fx
        this.imageDataContainer.setStyle({opacity: .0001});
        this.numberDisplay.hide();      
        
        if (this.imageArray.length <= 0)
        {
        	return;
        }
        
        
        if (this.isCurrentItemModal()==true)
        {
        	this.lightboxImage.hide();
        	
        	// Modal content - ajax req or cache
        	var link = this.imageArray[this.activeImage][0];
        	
    		if (this.modalCache[link] && this.modalCache[link] != "undefined")
    		{   
    			this.updateModal(this.modalCache[link]);
    		}
    		else
    		{
    			this.currentLink = link; // used to cache result of ajax request
				var req = new Ajax.Request(link,
				{
					method:'get',
					parameters: '',
					onComplete: this.processInfo.bindAsEventListener(this)
				});
    		}
        }
        else
        {
			var imgPreloader = new Image();
			
			// once image is preloaded, resize image container
			imgPreloader.onload = (function(){
				this.lightboxImage.src = this.imageArray[this.activeImage][0];
				/*Bug Fixed by Andy Scott*/
				this.lightboxImage.width = imgPreloader.width;
				this.lightboxImage.height = imgPreloader.height;
				/*End of Bug Fix*/
				this.resizeImageContainer(imgPreloader.width, imgPreloader.height);
			}).bind(this);
			imgPreloader.src = this.imageArray[this.activeImage][0];
        }
    },

    //
    //  resizeImageContainer()
    //
    resizeImageContainer: function(imgWidth, imgHeight) {

        // get current width and height
        var widthCurrent  = this.outerImageContainer.getWidth();
        var heightCurrent = this.outerImageContainer.getHeight();

        // get new width and height
        var widthNew  = (imgWidth  + LightboxOptions.borderSize * 2);
        var heightNew = (imgHeight + LightboxOptions.borderSize * 2);

        // scalars based on change from old to new
        var xScale = (widthNew  / widthCurrent)  * 100;
        var yScale = (heightNew / heightCurrent) * 100;

        // calculate size difference between new and old image, and resize if necessary
        var wDiff = widthCurrent - widthNew;
        var hDiff = heightCurrent - heightNew;

        if (hDiff != 0) new Effect.Scale(this.outerImageContainer, yScale, {scaleX: false, duration: this.resizeDuration, queue: 'front'}); 
        if (wDiff != 0) new Effect.Scale(this.outerImageContainer, xScale, {scaleY: false, duration: this.resizeDuration, delay: this.resizeDuration}); 

        // if new and old image are same size and no scaling transition is necessary, 
        // do a quick pause to prevent image flicker.
        var timeout = 0;
        if ((hDiff == 0) && (wDiff == 0)){
            timeout = 100;
            if (Prototype.Browser.IE) timeout = 250;   
        }

        if (this.imageArray.length > 0 )
        {
	        (function(){
	            this.prevLink.setStyle({ height: imgHeight + 'px' });
	            this.nextLink.setStyle({ height: imgHeight + 'px' });
	            this.imageDataContainer.setStyle({ width: widthNew + 'px' });
	
	            this.showImage();
	        }).bind(this).delay(timeout / 1000);
        }
    },
    
    //
    //  showImage()
    //  Display image and begin preloading neighbors.
    //
    showImage: function(){
        this.loading.hide();
        
        if (this.isCurrentItemModal())
        {
        	new Effect.Appear(this.lightboxHTML, { 
	            duration: this.resizeDuration, 
	            queue: 'end', 
	            afterFinish: (function(){ this.updateDetails(); }).bind(this) 
	        });
        }
        else
        {
        	new Effect.Appear(this.lightboxImage, { 
	            duration: this.resizeDuration, 
	            queue: 'end', 
	            afterFinish: (function(){ this.updateDetails(); }).bind(this) 
	        });
	        this.preloadNeighborImages();
        }
        
    },

    //
    //  updateDetails()
    //  Display caption, image number, and bottom nav.
    //
    updateDetails: function() {
    
        this.caption.show();
        
        if (this.imageArray.length > 1)
        {
        	this.staticNav.show();
        	
        	// if is not modal
	    	if (this.imageArray[this.activeImage][2]==false)
	    	{
	    		this.hoverNav.show();
	    	}
        }
        
    	if (this.imageArray.length != 0)
    	{
	        this.caption.update(this.imageArray[this.activeImage][1]).show();
	        
	        // if image is part of set display 'Image x of x' 
	        /*
	        if (this.imageArray.length > 1)
	        {
	        	if (this.imageArray[this.activeImage][2]==true) // Is Modal
	        	{
		            this.numberDisplay.update( LightboxOptions.labelModal + ' ' + (this.activeImage + 1) + ' ' + LightboxOptions.labelOf + '  ' + this.imageArray.length).show();
	        	}
	        	else
	        	{
		            this.numberDisplay.update( LightboxOptions.labelImage + ' ' + (this.activeImage + 1) + ' ' + LightboxOptions.labelOf + '  ' + this.imageArray.length).show();
	        	}
	        }
	        */
    	}
    	
        new Effect.Parallel(
            [ 
               // new Effect.SlideDown(this.imageDataContainer, { sync: true, duration: this.resizeDuration, from: 0.0, to: 1.0 }), 
                new Effect.Appear(this.imageDataContainer, { sync: true, duration: this.resizeDuration }) 
            ], 
            { 
                duration: this.resizeDuration, 
                afterFinish: (function() {
	                // update overlay size and update nav
	                var arrayPageSize = this.getPageSize();
	                this.overlay.setStyle({ width: arrayPageSize[0] + 'px', height: arrayPageSize[1] + 'px' });
	                this.updateNav();
                }).bind(this)
            } 
        );
    },

    isCurrentItemModal: function()
    {
    	if (this.imageArray.length > 0 && this.activeImage >= 0 && this.imageArray[this.activeImage] != "undefined" && this.imageArray[this.activeImage][2]==true)
    	{
    		return true;
    	}
    	
    	return false;
    },
    
    //
    //  updateNav()
    //  Display appropriate previous and next hover navigation.
    //
    updateNav: function() {

        if (this.imageArray.length != 0 && this.isCurrentItemModal() == false)
        {
        	this.hoverNav.show();               
        }

        // if not first image in set, display prev image button
        //if (this.activeImage > 0) this.prevLink.show();

        // if not last image in set, display next image button
        //if (this.activeImage < (this.imageArray.length - 1)) this.nextLink.show();
        
        if (this.imageArray.length > 1)
        {
        	this.nextLink.show();
        	this.prevLink.show();
        }
        
        this.enableKeyboardNav();
    },

    //
    //  enableKeyboardNav()
    //
    enableKeyboardNav: function() {
        document.observe('keydown', this.keyboardAction); 
    },

    //
    //  disableKeyboardNav()
    //
    disableKeyboardNav: function() {
        document.stopObserving('keydown', this.keyboardAction); 
    },

    //
    //  keyboardAction()
    //
    keyboardAction: function(event) {
    	
    	if (this.imageArray.length == 0)
    	{
    		return;
    	}
    	
        var keycode = event.keyCode;

        var escapeKey;
        if (event.DOM_VK_ESCAPE) {  // mozilla
            escapeKey = event.DOM_VK_ESCAPE;
        } else { // ie
            escapeKey = 27;
        }

        var key = String.fromCharCode(keycode).toLowerCase();
        
        if (key.match(/x|o|c/) || (keycode == escapeKey)){ // close lightbox
            this.end();
        } else if ((key == 'p') || (keycode == 37)){ // display previous image
            //if (this.activeImage != 0){
			if (this.imageArray.length > 1)
			{
            	this.disableKeyboardNav();
                this.changeImage(this.activeImage - 1);
            }
        } else if ((key == 'n') || (keycode == 39)){ // display next image
            //if (this.activeImage != (this.imageArray.length - 1)){
            if (this.imageArray.length > 1)
			{
                this.disableKeyboardNav();
                this.changeImage(this.activeImage + 1);
            }
        }
    },

    //
    //  preloadNeighborImages()
    //  Preload previous and next images.
    //
    preloadNeighborImages: function(){
    	
        var preloadNextImage, preloadPrevImage;
        if (this.imageArray.length > this.activeImage + 1){
            preloadNextImage = new Image();
            preloadNextImage.src = this.imageArray[this.activeImage + 1][0];
        }
        if (this.activeImage > 0){
            preloadPrevImage = new Image();
            preloadPrevImage.src = this.imageArray[this.activeImage - 1][0];
        }
    
    },

    //
    //  end()
    //
    end: function() {
        this.disableKeyboardNav();
        this.lightbox.hide();
        this.lightboxHTML.innerHTML = "";
        new Effect.Fade(this.overlay, { duration: this.overlayDuration });
        $$('select', 'object', 'embed').each(function(node){ node.style.visibility = 'visible' });
    },

    //
    //  getPageSize()
    //
    getPageSize: function() {
	        
	     var xScroll, yScroll;
		
		if (window.innerHeight && window.scrollMaxY) {	
			xScroll = window.innerWidth + window.scrollMaxX;
			yScroll = window.innerHeight + window.scrollMaxY;
		} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}
		
		var windowWidth, windowHeight;
		
		if (self.innerHeight) {	// all except Explorer
			if(document.documentElement.clientWidth){
				windowWidth = document.documentElement.clientWidth; 
			} else {
				windowWidth = self.innerWidth;
			}
			windowHeight = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
			windowWidth = document.documentElement.clientWidth;
			windowHeight = document.documentElement.clientHeight;
		} else if (document.body) { // other Explorers
			windowWidth = document.body.clientWidth;
			windowHeight = document.body.clientHeight;
		}	
		
		// for small pages with total height less then height of the viewport
		if(yScroll < windowHeight){
			pageHeight = windowHeight;
		} else { 
			pageHeight = yScroll;
		}
	
		// for small pages with total width less then width of the viewport
		if(xScroll < windowWidth){	
			pageWidth = xScroll;		
		} else {
			pageWidth = windowWidth;
		}

		return [pageWidth,pageHeight];
	},
	
};

// Initialize Lightbox
var myLightbox = null;
if (Prototype.Browser.IE)
{
	// This is to avoid operation aborted error in IE - modifying DOM before it is done loading
	Event.observe(window, 'load', function () { myLightbox = new IBLightbox(); document.fire('lightbox:loaded');}, false);
}
else
{
	document.observe('dom:loaded', function () { myLightbox = new IBLightbox(); document.fire('lightbox:loaded');});
}
