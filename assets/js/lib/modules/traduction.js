myApp.get_tradget_trad: function( code ) {
	
	var trad = CI[CI.language][code];
		
	if(typeof trad == 'undefined')
	{
		console.log('Aucune traduction pour ' + code + 'dans la langue : ' + CI.language);
	}

	return trad;
}