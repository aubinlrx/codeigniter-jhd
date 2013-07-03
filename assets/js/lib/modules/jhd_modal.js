/**
 * JHD MODAL (JQuery plugin)
 *
 *
 * Implémentation
 * ===================================================================
 * Méthodes disponibles :
 *     - Title   : Set le titre du modal
 *     - Show    : Affichage du modal
 *     - Close   : Masquage du modal via un button
 *     - Confirm : Action au clic d'un second button
 *
 * JAVASCRIPT
 * ==========
 * $(function() {
 *           $("#modal").jhdModal({
 *               bind: "a.modal",
 *               show: {
 *                   insertData: function(context, container) {
 *                       return "<h1>Un titre</h1>" +
 *                               "<p>lorem ipsum dolores</p>"
 *                   }
 *               },
 *               close: {
 *                   onClose: function(context) {
 *                       //alert("Ca ferme !!");
 *                   }
 *               },
 *               confirm: {
 *                   onConfirm: function(context) {
 *                       var options = {
 *                           onClose: function(context) {
 *                               //alert("Ca ferme apres confirm");
 *                           }
 *                       };
 *
 *                       context.jhdModal("close", context, options);
 *                   }
 *               }
 *           });
 *
 *
 * HTML
 * ====
 * <a data-modal="test" data-modal-title="Modal 2" name="test2" href="#test2" class="modal">Un lien de test</a>
 * 
 * <div id="test" style="display: none;">
 *     <h1 class="title">Test</h1>
 *     <div class="ajax-loading" style="display:none;">
 *         <img class="loader-ajax" src="<?= base_url()?>assets/img/loader-dot.gif" alt="loader pdf" />
 *     </div>
 *     <div class="modal-container">
 *         //Show s'affiche ici.
 *     </div>
 *     <div class="btn-container">
 *         <button class="confirm">valider</button>
 *         <button class="close">fermer</button>
 *     </div>
 * </div>
 *
 *
 * 
 */


(function($){

    var mainConfigs = {
        titre: null,
        bind: null,
        draggable: false,
        keypress: false,
        currentBind: false,
        clickOutside: true,
        top: 100,
        overlay: 0.5,
        container: '.modal-container',
        buttons : {
            close: ".close",
            confirm: ".confirm"
        },
        onShow: function( context, container, bind) {},
        onClose: function( context ) {},
        onConfirm: function( context, bind ) {},
        show: {},
        close: {},
        confirm: {},
        update: {}
    };

    var $this = $(this);

	var methods = {

    	init: function( options ) {

            mainConfigs = $.extend(mainConfigs, options);

            //Append overlay on DOM
            methods.generate_overlay();

            if(mainConfigs.draggable === true)
            {
                $(this).draggable();
            }

    		return this.each(function(){

    			$this = $(this);

                if(mainConfigs.bind !== null)
                {
                    $(mainConfigs.bind).click(function(e){
                        e.preventDefault();
                        mainConfigs.currentBind = $(this);
                        var title = $(this).data('modal-title');
                        if(typeof title !== "undefined" && title !== "")
                        {
                            mainConfigs.title = title;
                        }
                        methods.update_title($this);
                        methods.show($this, options.show, mainConfigs.currentBind);
                    });
                }
                else
                {
                    methods.update_title($this);
                    methods.show($this, options.show, mainConfigs.currentBind);
                }

                if(mainConfigs.clickOutside == true)
                {
                    //Click exterieur
                    $('body').undelegate("#lean_overlay", 'click').delegate("#lean_overlay", 'click', function(e) {
                        e.preventDefault();
                        methods.close($this, mainConfigs.close);
                    });
                }

                if(mainConfigs.keypress == true)
                {
                    Mousetrap.bind('escape', function() { 
                        methods.close($this, mainConfigs.close);
                    });

                    Mousetrap.bind('enter', function() {
                        methods.confirm($this, mainConfigs.confirm, mainConfigs.currentBind);
                    });   
                }

                //Click close
                $this.undelegate(mainConfigs.buttons.close, 'click').delegate(mainConfigs.buttons.close, 'click', function(e) { 
                    e.preventDefault();
                    methods.close($this, mainConfigs.close);              
                });

                //Click confirm
                $this.undelegate(mainConfigs.buttons.confirm, 'click').delegate(mainConfigs.buttons.confirm, 'click', function(e) {
                    e.preventDefault();
                    methods.confirm($this, mainConfigs.confirm, mainConfigs.currentBind);
                });

    		})
    	},

    	show: function( context, options, bind ) {

            var configs = {
                container: ".modal-container"
            };

            configs = $.extend(configs, options);

            var modal_height = $this.outerHeight();
            var modal_width = $this.outerWidth();

            methods.display_overlay($this);

            $this.css({ 
            
                'display' : 'block',
                'position' : 'fixed',
                'opacity' : 0,
                'z-index': 11000,
                'left' : 50 + '%',
                'margin-left' : -(modal_width/2) + "px",
                'top' : mainConfigs.top + "px"
            
            });

            var $container = $this.find(configs.container);
            var data = mainConfigs.onShow($this, $container, bind);
            $container.html(data);

            $this.fadeTo(200,1);

    	},

    	close: function( context, options ) {

            $("#lean_overlay").fadeOut(200);

            $this.css({ 'display' : 'none' });

            mainConfigs.currentBind = false;
            mainConfigs.onClose($this);
    	},

    	confirm: function( context, options, bind ) {

            mainConfigs.onConfirm($this, bind);
    	},

        update: function( context, options ) {

            mainConfigs.onUpdate($this);

        },

        update_title: function ( context ) {
            //Mise à jour du titre
            if(mainConfigs.title !== null)
            {

                $this.find('.title').html(mainConfigs.title);
            }
        },

        generate_overlay: function() {
            var overlay = $("<div id='lean_overlay'></div>");

            $("body").append(overlay);
        },

        display_overlay: function( context ) {
            $('#lean_overlay').css({ 

                'display' : 'block', 
                opacity : 0

            });
            $('#lean_overlay').fadeTo(200, mainConfigs.overlay);
        }
    };

	$.fn.jhdModal = function( method ) {

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