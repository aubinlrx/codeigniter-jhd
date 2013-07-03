myApp.ajax = {
	
	config: {
		type: 'POST',
		data: {},
		dataType: 'json',
		success: function () { },
		error: function() { }
	},

	extend: function(options) {
		$.extend(this.config, options);
	},

	apiCall: function(options) {

		$.extend(myApp.ajax.config, options);

 		$.ajax({
 			type: myApp.ajax.config.type,
	 		url: myApp.ajax.config.url,
	 		contentType: myApp.ajax.config.contentType,
	 		data: myApp.ajax.config.data,
	 		success: function(result) {
	 			if(result.error == 'ajax_disconnected')
	 			{
	 				alert(result.message);
	 				document.location.reload(true);
	 			}
	 			myApp.ajax.config.success(result);
	 		},
	 		error: function(result) {
	 			myApp.ajax.config.error(result);
	 			alert('An error occured during the operation');
	 			//autre function pour gérer les erreurs à voir.. 
	 		}
 		})
 	}
}