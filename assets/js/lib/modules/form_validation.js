$(document).ready(function(){

	myApp.form_validation = {

		inputs: [],
		$input: false,
		value : false,
		label: false,
		rules: [],
		errors: {error: false, rules: []},

		init: function(){
			this.inputs = [];
			this.$input = false;
			this.value = false;
			this.label = false;
			this.rules = [];
			this.errors = {error: false, rules: []};
		},

		validate: function(arr){

			this.init();
			this.inputs = arr;

			for( i = 0; i<this.inputs.length; i++)
			{
				this.$input = this.inputs[i].input;
				this.value = (this.$input.val() == '') ? this.inputs[i].def : this.$input.val();
				this.label = this.inputs[i].label;
				if(this.getRules() == true)
				{
					this.run(this.inputs[i]);
				}
			}

			return this.errors;
		},

		run: function(){
			var func = null;
			var params = null;
			var msg = false;

			for(var i = 0; i < this.rules.length; i++)
			{
				func = this.rules[i].name;
				params = this.rules[i].parameters;
				
				if(typeof this[func] == 'function') 
				{
					if(params.length > 0)
					{
						msg = this[func].apply(this, Array.prototype.slice.call(params, 0));
					}
					else
					{
						msg = this[func]();
					}

					if(msg !== false)
					{
						this.addErrors(func, msg);
					}
				}
				else
				{
					console.log(func + "is not a valid function")
				}
			}
		},

		getRules: function(){

			var matches = null;
			var name = null;
			var regex = /\@([^[\]]+)(?:\[(.*?)\])?/;
			var rules = this.$input.data('validate');
				
			if(typeof rules == "undefined" || rules == null){
				console.log("no definition of data-validate for : " + this.label);
			}else {
				rules = rules.split(';');

				if(typeof rules == "undefined" || (rules.length == 1 && rules[0] == ""))
				{
					console.log("no rule for : " + this.label);
				}
				else
				{
					for(var i = 0; i<rules.length; i++)
					{
						matches = regex.exec(rules[i]);
						this.rules[i] = {name: matches[1], parameters: []};

						if ("undefined" !== typeof matches[2] && matches[2] !== "") 
						{
						  	var params = matches[2].split('|');
						  	if(params.length > 0)
						  	{
						  		for(var x = 0; x < params.length; x++)
								{
							  		this.rules[i].parameters.push(params[x]);
								}
						  	}
						}
					}
				}
			}

			return true;
		},

		addErrors: function(name, message){
			this.errors.error = true;
			if(typeof this.errors.rules[this.label] == 'undefined')
			{
				this.errors.rules[this.label] = [];
			}
			this.errors.rules[this.label].push({error: true, msg: message, label: name});
		},

		NotEmpty: function(){
			
			var message = "Le champs est vide";

			if($.trim(this.value) == "")
			{
				return message;
			}

			return false;
		},

		HasDecimal: function(int){
			
			var message = "Le champs ne doit pas comporter plus de " + int + " nombre après la virgule";

			if(this.value.toString().replace(/^-?\d*\.?|0+$/g, '').length != int)
			{
				return message;
			}

			return false;
		}
	}
});