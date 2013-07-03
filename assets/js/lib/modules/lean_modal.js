(function($){
 
    $.fn.extend({ 
         
        jhdModal: function(options) {
 
            var configs = $.extend({
				            top: 100,
				            overlay: 0.5,
				            container: '.modal-container',
				            close: {
				            	btn: null,
				            	onClose: function(ui){},
				            },
				            confirm: {
				            	btn: null,
				            	url: null,
				            	options: {},
				            	beforeSend: function(ui){},
				            	onComplete: function(data, ui){},
				            	onError: function(error, ui){},
				            	loader: null
				            },
				            show: {
				            	url: null,
				            	options: {},
				            	beforeSend: function(ui){},
				            	onComplete: function(data, ui){},
				            	onError: function(error, ui){},
				            	loader: null
				            }
				        }, options);

            var overlay = $("<div id='lean_overlay'></div>");
            
            $("body").append(overlay);
                 
            options = configs;

            return this.each(function(){

            	var $link = '.' + $(this).attr('class');         	

            	$('body').delegate($link, 'click', function(e){
	                e.preventDefault();

	                //Initialisation des variables
	                var o = options;
	                var modal_id = '#' + $(this).data("modal");
	           		var $el = $(this);
	               	
	               	//Display le modal
	               	show(modal_id, $el, o);

	            }); 

            });

			function show(modal_id, $trigger, o){

				var modal_height = $(modal_id).outerHeight();
        	  	var modal_width = $(modal_id).outerWidth();
        	  	var $container = $(modal_id).find(o.container);

        		$('#lean_overlay').css({ 'display' : 'block', opacity : 0 });

        		$('#lean_overlay').fadeTo(200,o.overlay);

        		$(modal_id).css({ 
        		
        			'display' : 'block',
        			'position' : 'fixed',
        			'opacity' : 0,
        			'z-index': 11000,
        			'left' : 50 + '%',
        			'margin-left' : -(modal_width/2) + "px",
        			'top' : o.top + "px"
        		
        		});

        		$(modal_id).fadeTo(200,1);

        		//Click exterieur
               	$('body').undelegate("lean_overlay").delegate("#lean_overlay", 'click', function(e) {
	            	e.preventDefault();
	            	close_modal(modal_id, $container, o);
	            });

               	//Click close
               	$(modal_id).undelegate(o.close.btn, 'click').delegate(o.close.btn, 'click', function(e) { 
	            	e.preventDefault();
	            	close_modal(modal_id, $container, o);                 
	            });

               	//Click confirm
	            $(modal_id).undelegate(o.confirm.btn, 'click').delegate(o.confirm.btn, 'click', function(e) {
	            	e.preventDefault();
		            confirm_modal(modal_id, $container, o);
	            });

	            //Show ajax si définie
        		if(o.show.url !== null)
        		{
        			var options = o.show;

        			ajax(modal_id, $container, options);
        			
        		}
			}

			function close_modal(modal_id, $container, options){

				options = options.close;

				console.log('test');

        		$("#lean_overlay").fadeOut(200);

        		$(modal_id).css({ 'display' : 'none' });

        		options.onClose($container);

			}

			function confirm_modal(modal_id, $container, options){

				options = options.confirm;

				options.onConfirm($container);

				if(options.url != null)
				{
					ajax(modal_id, $container, options);
				}
			}

			function ajax(modal_id, $container, options){

				var config = {
					url: '' || options.url,
					type: 'POST',
					data: {},
					beforeSend: function() {},
					success: function() {},
					error: function() {}
				};

				var classLoader = (typeof options.loader == "undefined") ? ".ajax-loading" : options.loader.img;

				$.extend(config, options.options);

				xhr = $.ajax({
						type: config.type,
						url: config.url,
						contentType: config.contentType,
						data: config.data,
						beforeSend: function() {
							options.beforeSend($container);
							$(modal_id).find(classLoader).show();
						},
						success: function(result) {
							options.onComplete(result, $container);
						},
						error: function(error) {
							options.onError(error, $container);
							alert("An error has occured during the operation..");
						},
						complete: function() {
							$(modal_id).find(classLoader).hide();
						}
					});
			}
    
        }
    });
     
})(jQuery);