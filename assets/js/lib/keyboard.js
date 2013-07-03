(function($){

	var mainConfigs = {
		type: "numbers",
		keyboard: '.keyboard',
		textarea: '.write',
		draggable: false,
		bind: null,
		shift: false,
		capslock: false,
		character: null
	};

	var methods = {

		init: function( options ) {
			mainConfigs = $.extend(mainConfigs, options);

			if(mainConfigs.draggable === true)
			{
				$(this).draggable();
			}

			return this.each(function(){

				var $this = $(this);
				var $write = $this.find(mainConfigs.textarea);
				var keyboard = mainConfigs.keyboard + ' li';

				$this.find(mainConfigs.keyboard).hide();
				$this.find('.' + mainConfigs.type).show();

				$this.on('change', $write, function(e){
					
				});

				$write.bind('input propertychange', function() {
					methods.check_dot($write);
				});

				if(($write).is(':focus')){
					Mousetrap.bind(['1','2','3','4','5','6','7','8','9','0','.'], function(e) { 
						//
	                });
				}

				$this.on('click', keyboard, function(e){
					e.preventDefault();

					if(!$(this).hasClass('disable')){
						mainConfigs.character = $(this).html();

						methods.shift_keys($write, this);
						methods.capslock($write, this);
						methods.delete_last($write, this);
						methods.special_char($write, this);
						methods.uppercase($write, this);
						methods.remove_shift($write, this);
						
						$write.val($write.val() + mainConfigs.character);
					}

					methods.check_dot($write);
				});

			})
		},

		shift_keys: function ( context, key ) {
			$this = $(key);

			if($this.hasClass('left-shift') || $this.hasClass('right-shift')) {
				$('.letter').toggleClass('uppercase');
				$('.symbol span').toggle();

				mainConfigs.shift = (mainConfigs.shift === true) ? false : true;
				mainConfigs.capslock = false
				return false;
			}
		},

		capslock: function ( context, key ) {
			$this = $(key);

			if($this.hasClass('capslock')) {
				$('.letter').toggleClass('uppercase');
				mainConfigs.capslock = true;
				return false;
			}
		},

		delete_last: function ( context, key ) {
			$this = $(key);
			$write = context;

			if($this.hasClass('delete')) {
				var content = $write.val();
				mainConfigs.character = '';

				$write.val(content.substr(0, content.length - 1));
				return false;
			}
		},

		special_char: function( context, key ) {
			$this = $(key);

			if ($this.hasClass('symbol')) mainConfigs.character = $('span:visible', $this).html();
			if ($this.hasClass('space')) mainConfigs.character = ' ';
			if ($this.hasClass('tab')) mainConfigs.character = "\t";
			if ($this.hasClass('return')) mainConfigs.character = "\n";
		},

		uppercase: function( context, key ) {
			$this = $(key);

			if ($this.hasClass('uppercase')) mainConfigs.character = mainConfigs.character.toUpperCase();
		},

		remove_shift: function ( context, key ) {
			$this = $(key);

			if (mainConfigs.shift === true) {
				$('.symbol span').toggle();
				if (mainConfigs.capslock === false) $('.letter').toggleClass('uppercase');
				
				mainConfigs.shift = false;
			}
		},

		check_dot: function ( context ) {
			var $dot = $('.symbol.dot');
			
			if (/\./.test(context.val())) {
				if(!$dot.hasClass('disable'))
				{
					$dot.toggleClass('disable');
				}
			}

			if(/\:/.test(context.val())) {
				if(!$dot.hasClass('disable'))
				{
					$dot.toggleClass('disable');
				}
			}

			else
			{
				if($dot.hasClass('disable'))
				{
					$dot.toggleClass('disable');
				}
			}
		}
	}

	$.fn.jhdKeyboard = function( method ) {

		// Method calling logic
	    if ( methods[method] ) 
	    {
	      	return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
	    } 
	    else if ( typeof method === 'object' || ! method ) 
	    {
	      	return methods.init.apply( this, arguments );
	    } else 
	    {
	      	$.error( 'Method ' +  method + ' does not exist on jQuery.jhdModal' );
	    }   
	};

})(jQuery);

/*$(function(){
	var $write = $('#write'),
		shift = false,
		capslock = false;
	
	$('#keyboard li').click(function(){
		var $this = $(this),
			character = $this.html(); // If it's a lowercase letter, nothing happens to this variable
		
		// Shift keys
		if ($this.hasClass('left-shift') || $this.hasClass('right-shift')) {
			$('.letter').toggleClass('uppercase');
			$('.symbol span').toggle();
			
			shift = (shift === true) ? false : true;
			capslock = false;
			return false;
		}
		
		// Caps lock
		if ($this.hasClass('capslock')) {
			$('.letter').toggleClass('uppercase');
			capslock = true;
			return false;
		}
		
		// Delete
		if ($this.hasClass('delete')) {
			var html = $write.val();
			
			$write.val(html.substr(0, html.length - 1));
			return false;
		}
		
		// Special characters
		if ($this.hasClass('symbol')) character = $('span:visible', $this).html();
		if ($this.hasClass('space')) character = ' ';
		if ($this.hasClass('tab')) character = "\t";
		if ($this.hasClass('return')) character = "\n";
		
		// Uppercase letter
		if ($this.hasClass('uppercase')) character = character.toUpperCase();
		
		// Remove shift once a key is clicked.
		if (shift === true) {
			$('.symbol span').toggle();
			if (capslock === false) $('.letter').toggleClass('uppercase');
			
			shift = false;
		}
		
		// Add the character
		console.log($write.val());
		$write.val($write.val() + character);
	});
});*/