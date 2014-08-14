/*
 * Extend string class
 */
String.prototype.getQueryString = function() {
	var parts = this.split('?');
	if (typeof parts[1]=='undefined')
		return '';
	return parts[1];
}

/*
 * EM Layer
 *
 * class for layered navigation
 * Depends on library: prototype, jquery.address
 */
var EM_LayeredNavigation = Class.create();
EM_LayeredNavigation.prototype = {
	initialize: function(options) {
		this.endpoint = options.endpoint;
		this.category = options.category;
		this.enableAjax = options.ajax;
		this.layer = '.block-layered-nav';
		this.products = '.category-products';
		this.selectors = [
			this.layer + ' a',                  // layer link
			this.layer + ' input',              // radio, checkbox
			this.layer + ' select',             // select
			this.products + ' .view-mode a',    // view mode (grid/list)
			this.products + ' .sort-by a',      // sort direction
			this.products + ' .limiter select', // limiter
			this.products + ' .sort-by select', // sort by
			this.products + ' .pages a'         // pagination
		];

		this.setupState(options.reqUri);
		this.setupEvent();
	},
	
	setupEvent: function() {
		var self = this;
		// perform update on input change
		$$(this.selectors.join(',')).each(function(s,i) {
			switch (s.tagName) {
				case 'A':
					s.href = self.replaceUrl(s.href);
					$(s).observe('click', function (event) {
						event.preventDefault();
						self.update(this.href);
					});
					break;
				case 'SELECT':
					$(s).select('option').each(function (o, idx) {
						o.value = self.replaceUrl(o.value);
					});
					
					s.onchange = function () {
						self.update(this.value);
					}
					break;
				case 'INPUT':
					s.value = self.replaceUrl(s.value);
					$(s).observe('click', function (event) {
						self.update(this.value);
					});
					break;
			}
		});
	},
	
	replaceUrl: function(url) {
		var query = url.getQueryString();
		var base = document.URL.replace(/\?.*/, '');
		return base + '?' + query;
	},
	
	update: function(url) {
		var baseUrl = this.endpoint + 'id/' + this.category + '/';
		var ajaxUrl = baseUrl;
		var query = url.getQueryString();
		if (query!='')
			ajaxUrl += '?' + query;
		
		if (!this.enableAjax) {
			window.location = url;
			return;
		}
		$('loading-mask').show();
		var self = this;
		new Ajax.Request(ajaxUrl,  {
			onSuccess: function (data) {
				var response = data.responseJSON;
				self.prepareBlock();
		console.log(ajaxUrl);
				$$(self.layer).first().replace(response.layer);
				$$(self.products).first().replace(response.products);
					self.setState(query);	// set query string
				self.prevQuery = query;
				self.setupEvent();               
				$('loading-mask').hide();
			}
		});
	},
    
    setupState: function(reqUri) {
	if (jQuery.browser.msie) return;
		var arr = reqUri.split('?');
		var state = arr[0];
		this.prevQuery = typeof arr[1]!=='undefined' ? arr[1] : '';
		jQuery.address.state(state);
		jQuery.address.externalChange(this.onStateChange.bind(this));
	},
	
	setState: function(query) {
		if (jQuery.browser.msie) return;
		jQuery.address.queryString(query);
	},
	
	onStateChange: function(event) {
		if (event.queryString==this.prevQuery)
			return;
		var query = event.queryString;
		jQuery.address.queryString(query);
		this.update('?'+query);
	},
	
	prepareBlock: function() {
		// add products block if not exists
		
		if (!$$(this.products).length || $$('.block-layered-nav').length != 0) {
			var className = this.products.replace('.', '');
			var divString = '<div class="'+className+'"></div>';console.log(divString);
			if($$('.col-main').first().insert({ bottom:  divString})){
			
		console.log(className);}
			// remove block note-msg (fix bug empty product count)
			$$('.note-msg').each(function (e) {
				e.remove();
			});
		}
		console.log('test13422');
	}
};
