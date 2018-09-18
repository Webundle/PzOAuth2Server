var dropdown = (function() {

	var dTypes = ['simple', 'selection', 'inline', 'input'];
	
	var theDropdown = function(selector, config){

		// Dropdown Type
		this.type;

		this.$el;

		this.defaultText = "";

		this.menu = "";

		this.icon = "";

		this.menuStart = 2;

		this.content = "";

		this.hiddenInput = "";

		this.optionContent = "";
		
		// Event of dropdown
		this.events = {
			"click .text": "toggleMenu",
			// "mouseover .text": "showMenu",
			"click .dropdown.icon": "toggleMenu",
			"click .menu .item": ["selectItem", "toggleMenu"],
			"keyup .input": "proposal"
			// "focusout .input": "hideMenu"
		};

		
		/**
		 * Show the dropdown
		 */
		this.showMenu = function(){
			this.$el.addClass("active visible");
		};

		/**
		 * Show the dropdown
		 */
		this.hideMenu = function(){
			this.$el.removeClass("active visible");
		};


		this.toggleMenu = function(){
			// console.log("Menu show");
			switch (this.type) {
				case "selector":
					this.$el.toggleClass("active visible");
					break;
				case "inline":
					this.$el.toggleClass("active visible");
					break;
				case "input":
					return;
					break;
				default:
					this.$el.toggleClass("active");
			}
		};

		
		/**
		 * Show the dropdown
		 */
		this.selectItem = function(event){
			var selectedItem = $( event.target );
			// console.log("je");
			switch (this.type) {
				case "selection":
					this.hiddenInput.val(selectedItem.attr('data-value'));
					this.defaultText.html(selectedItem.html());
					break;
				case "input":
					this.hiddenInput.val(selectedItem.html());
					this.hideMenu();
					break;
				case "inline":
					this.defaultText.html(selectedItem.html());
					break;			
			}
		};

		this.proposal = function(event){
			if(this.hiddenInput.val().length >= this.menuStart)
			{
				this.menu.find(".item").each(function(){
					$(this).removeClass("hidden");
				});

				var self = this;
				var inputVal = self.hiddenInput.val().toLowerCase();
				// console.log(inputVal);
				this.menu.find(".item").each(function(){
					var inputMatch = $(this).html().toLowerCase().match("\\b" +inputVal) ;
					if (inputMatch instanceof Array) {
						// $(this).addClass("hidden");
						// console.log($(this).html());
					} else{
						$(this).addClass("hidden");
					}
				});

				// var matches = patInputVal.exec(text);
				this.showMenu();

			} else{
				this.hideMenu();
			}

		};


		/**
		 * eventDelegates
		 */
		this.delegateEvents = function(){
			var self = this;

			for (var prop in self.events)
			{
				var patEvent = prop.match(/\w{0,}\s/)[0].trim();
				var patselector = prop.match(/\s#{0,}.{0,}\w{0,}/)[0].trim();

				// console.log(patEvent + " " +patselector);
				if(self.events[prop] instanceof Array){
					var fn = self.events[prop];
					for (var i = 0; i < fn.length; i++) {
						this.$el.delegate(patselector, patEvent, self[fn[i]].bind(self));
					}
				} else{
					this.$el.delegate(patselector, patEvent, self[self.events[prop]].bind(self));
				}
			}
		};
		
		/**
		 * undelegateEvents
		 */
		// this.undelegateEvents = function(){
		// 	for (var prop in this.events)
		// 	{
		// 		var patEvent = prop.match(/\w{0,}\s/);
		// 		var patselector = prop.match(/\s#{0,}.{0,}\w{0,}/);
		// 		this.$el.undelegate(patselector, patEvent, this[this.events[prop]]);
		// 	}
		// }

		// Test if is dropdown
		for (var i = 0; i < dTypes.length; i++) {
			if(selector.hasClass(dTypes[i]))
			{
				this.type = dTypes[i];
				this.$el = selector;
			}
		};

		if(this.type)
		{
			this.build();
			this.delegateEvents();
		}
	};


	/**
	 * Render the dropdown
	 */
	theDropdown.prototype.build = function() {

		switch (this.type) {
			case "input":
				this.menu = this.$el.find(".menu");
				this.hiddenInput = this.$el.find("input");
				break;
			default:

				this.defaultText = this.$el.find(".text");
				this.menu = this.$el.find(".menu");
				this.icon = this.$el.find(".dropdown.icon");

				if(this.icon){
					this.icon.html('<i class="fa fa-caret-down"></i>');
				}

				if(this.$el.find("input[type='hidden']"))
				{
					this.hiddenInput = this.$el.find("input[type='hidden']");
				}
		}
	}


	var makeDropdrown = function (selector){
		var dropDown = new theDropdown(selector);
	}

    return { 
		makeDropdrown : makeDropdrown
	};

})();