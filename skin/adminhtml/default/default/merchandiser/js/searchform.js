function submitSearch(form) {
	var query = $('search').value;
	sendAjax(form.action+'?name='+query, form.method, 'search-results');
}

function sendAjax(action, method, output_id) {
	if (!method) { 
		method = 'post'; 
	}
	if (!output_id) {
		output_id = 'searchRequestResult';
	}
	var req = new Ajax.Request(action, {
		'method' : method,
		'onSuccess' : function(transport) {
			hideLoader();
			$(output_id).update(transport.responseText);
			hideDuplicates();
			observeCategoryAdd();
		},
		'onFailure' : function(transport) {
			hideLoader();
			$(output_id).update(transport.responseText);
		}
	});
	showLoader();
	$(output_id).childElements().each( function(item) {
		item.remove();
	});
}

function loadFeature(action)
{
	var req = new Ajax.Request(action, {
		method : 'post',
		parameters: {
            show: 'intro'
        },
		onSuccess : function(transport) {
		hideLoader();
		$('featureInfo').update(transport.responseText);
	}
	});
	showLoader();
}

function showLoader(sMaskId) {
	if (!sMaskId) {
		sMaskId = 'loading-mask';
	}
	$(sMaskId).style.display = 'block';
}

function hideLoader() {
	$('loading-mask').style.display = 'none';
}