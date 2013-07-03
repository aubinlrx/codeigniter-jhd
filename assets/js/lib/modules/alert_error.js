$(document).ready(function(){
	myApp.alert_error = {

		show: function( data ) {
			if(typeof data == 'object')
			{
				var message = "";
				$.each(data, function(k,v){
					message = message + v + "\n";
				})
				alert(message);
			}
			else
			{
				alert(data);
			}
		}

	}
});