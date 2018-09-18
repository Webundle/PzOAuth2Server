var TabManager = (function($, _, is){
	
	"use strict";
	

	var Tab = function(selector){
		if(!selector.hasClass('tab')){
			return;
		}
		this.el = selector;
		this.tabActive = selector.find('.tab-content.active');
	};
	
	Tab.prototype = {
		show: function(tab){
			var theTab = this.el.find(tab);
			if(theTab.hasClass('tab-content')){
				theTab.addClass('active');
				this.tabActive = tab;
			}
		},
		hide: function(tab){
			var theTab = this.el.find(tab);
			if(theTab.hasClass('tab-content')){
				theTab.removeClass('active');
			}
		},
		active: function(tab){
			this.hide(this.tabActive);
			this.show(tab);
		}
	};
	
	var build = function(selector){
		return new Tab(selector);
	};

	return {
		build: build
	};

})($, _, is);