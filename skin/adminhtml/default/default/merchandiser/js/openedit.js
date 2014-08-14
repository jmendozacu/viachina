Event.observe(window, 'load', function() {
	window.lastLoadedNode = null;
	window.refreshed = true;
    Ajax.Responders.register({
    	onComplete: function() {
            var category_id = tree.currentNodeId;
            var store_id = tree.storeId;
            var categoryTitle = $$('#category-edit-container .icon-head.head-categories')[0].innerHTML; 
            if (window.refreshed || !categoryTitle.match(/\(ID.*\d+\)/)) {
            	category_id =0;
            	window.refreshed = false;
            }
        	$('merchandiser.open.category_id').value = category_id;
        	$('merchandiser.open.store_id').value = store_id;
            if (2 >= category_id) {
            	$('merchandiser.open.button').addClassName('disabled');
            } else {
            	$('merchandiser.open.button').removeClassName('disabled');
            }
    	}
    });

    Event.observe($('merchandiser.open.button'), 'click', function() {
    	openPopup();
    });

});

function openPopup() {
    var category = $('merchandiser.open.category_id');
    var store = $('merchandiser.open.store_id');
    var window_height = screen.height*0.8;
    if (0 != category.value) {
        var form = $('merchandiser.open.form');
        var url = form.action+'?'+category.name+'='+category.value+'&'+store.name+'='+store.value;
        var windowParams = 'menubar=no,location=no,width=1064px,height='+window_height+'px,left=100px,scrollbars=yes';
		window.lastLoadedNode = category.value;
        window.open(url, 'merchandiser_category', windowParams);
    }
}

function updateCurrentPage() {
	if (window.lastLoadedNode) {
        tree.categoryClick(tree.getNodeById(window.lastLoadedNode));
	}
}
	function changeMerchandiseOption(val){
		if(val == 1){
			$$('div.smartmerch-content').each(function(item){
				item.style.display = 'none';
			});
			$('smart_products_grid').style.display = 'none';
			//$$('div.visualmerch-content')[0].style.display = 'block';
			$$('div.visualmerch-content').each(function(e){
				e.style.display = 'block';
			});
		}else 
		//if(val==2)
		{
			$$('div.smartmerch-content').each(function(item){
				item.style.display = 'block';
			});
			$('smart_products_grid').style.display = 'block';
			//$$('div.visualmerch-content')[0].style.display = 'none';
			$$('div.visualmerch-content').each(function(e){
				e.style.display = 'none';
			});
		}
		
	}
	
	function visualMerchandiserCapture(url,catId){
		new Ajax.Request(url,
		{
	 		method:'post',
	 		parameters: { category_id:catId},
	 		onSuccess: function(transport){
	 			//var response = transport.responseText;
	 			tree.categoryClick(tree.getNodeById(catId));
	   		},
	 		onFailure: function(){  }
		});
		
	}

	