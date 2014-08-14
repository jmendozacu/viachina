/*
 * EM TreeView
 *
 * class for category filter
 * Depends on library: prototype
 *
 */
var EM_TreeView = Class.create();
EM_TreeView.prototype = {
	initialize: function(container) {
		this.element = null;
		this.childs = [];
		this.parent = null;

		this.attach(container);
	},

	attach: function(element) {
		this.element = $(element);
		var parent = this;
		if (!this.element.down('ol'))
			return;
			
		this.element.down('ol').childElements().each(function(e) {
			var child = new EM_TreeItem();
			parent.childs.push(child);
			child.parent = parent;
			child.attach(e);
		});
		this._decorate();
	},
	
	collapseAll: function() {
		EM_TreeView.recursiveCollapse(this);
	},
	
	expandAll: function() {
		EM_TreeView.recursiveExpand(this);
	},
	
	getSelect: function() {
		return this.curSel;
	},
	
	select: function() {
		if (this.childs.length>0) this.childs[0].select();
	},
	
	_decorate: function() {
		var childElements = this.element.down('ol').childElements();
		childElements.each(function(e) {
			e.removeClassName('first last');
		});
		childElements[childElements.length-1].addClassName('last');
		if (childElements.length > 1) childElements[0].addClassName('first');
	}
}

EM_TreeView.recursiveExpand = function(node) {
	node.childs.each(function (item) {
		item.expand();
		EM_TreeView.recursiveExpand(item);
	});
}

EM_TreeView.recursiveCollapse = function(node) {
	node.childs.each(function (item) {
		item.collapse();
		EM_TreeView.recursiveCollapse(item);
	});
}

/* EM_TreeItem class */
var EM_TreeItem = Class.create();
EM_TreeItem.prototype = {
	initialize: function() {
		this.childs = [];
		this.element = null;
		this.parent = null;
	},

	attach: function(element) {
		this.element = element;
		var parent = this;
		if (!this.element.down('ol')) return;
		this.element.down('ol').childElements().each(function(e) {
			var child = new EM_TreeItem();
			parent.childs.push(child);
			child.parent = parent;
			child.attach(e);
		});
		
		this._decorate();
	},
	
	collapse: function() {
		if (!this.hasChild()) return;
		this.element.removeClassName('expanded').addClassName('collapsed');
		this.element.down('ol').hide();
	},
	
	expand: function() {
		if (!this.hasChild()) return;
		this.element.removeClassName('collapsed').addClassName('expanded');
		this.element.down('ol').show();
	},
	
	toggle: function() {
		if (this._isExpand()) this.collapse(); else this.expand();
	},
	
	hasChild: function() {
		return this.childs.length > 0;
	},

	select: function() {
		this.element.children('.label').click();
	},
	
	_decorate: function() {
		var node = this;
		if (!this.hasChild()) return;
		if (!this.element.down('>.icon'))
			this.element.insert({top: EM_TreeItem.createIconElement(this)});
			
		var childElements = this.element.down('ol').childElements();
		childElements.each(function(e) {
			e.removeClassName('first last');
		});
		childElements[childElements.length-1].addClassName('last');
		if (childElements.length > 1) childElements[0].addClassName('first');
	},
	
	_isExpand: function() {
		return this.element.down('ol').visible();
	}
	
}

EM_TreeItem.createIconElement = function(node) {
	var icon = document.createElement('div');
	icon.className = 'icon';
	icon.innerHTML = " ";
	icon.onclick =  function() { node.toggle(); };
	//icon.addClassName('icon');
	//icon.observe('click', function() { node.toggle(); });
	return icon;
}

