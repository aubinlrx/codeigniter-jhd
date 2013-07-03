$(document).ready(function(){

	myApp.alert_message = {
		notification: '<div class="alert alert-block alert-success" style="display:none;"><button type="button" class="close" data-dismiss="alert">×</button></div>',
		notification_class: {error: "alert-error", success: "alert-success", warning: "alert-danger"},
		$container: null,

		config: {
			type: "",
			container: false,
			time: false,
			timeOut: false
		},

		specialTimeOut : {

		},

		show: function(message, options) {

			$.extend(this.config, options);

			this.$container = null;
			var $new_alert = null;

			if(this.config.container == false)
			{
				this.$container = $("#alert-message");
			}
			else
			{
				this.$container = $("#" + this.config.container);
			}

			if(this.$container.length == 1) {
				$new_alert = this.$container.append(this.notification);
				$new_alert.addClass(this.notification_class.error[this.config.type]);
				$new_alert.html(message);
				

				if(this.config.time == false) 
				{
					$new_alert.show();
				} 
				else
				{
					this.$container.show();

					/*this.timeOut = setTimeout(function() {
						myApp.alert_message.$container.hide();
						this.timeOut = false;
					}, this.config.time);*/

					if(this.timeOut !== false)
					{
						this.changeTimeOut(this.config.time);
					}
					else {
						this.triggerTimeOut(this.config.time);
					}
					
				}
			}

		},

		hideAlert: function() {
			this.$container.hide();
			this.timeOut = false;
		},

		triggerTimeOut: function(delay) {
			this.stopTimeOut();
			console.log(delay);
			this.timeOut = setTimeout(function(){
				myApp.alert_message.hideAlert();
			}, delay);
			return this;
		},

		stopTimeOut: function() {
			if(this.timeOut !== false)
			{
				clearTimeout(this.timeOut);
				this.timeOut = false;
			}
			return this;
		},

		changeTimeOut: function(delay) {
			this.config.time += delay;
			this.triggerTimeOut(this.config.time);
			return this;
		}
	};
});