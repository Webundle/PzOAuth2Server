var PageHeader = (function($, _, is){
	
	"use strict";
	
	var page_header = $('.region-header.page-header');
	
	var state = "hide";
	
	
	var _changeState = function(st){
		state = st;
		console.log(state);
	};
	
	var show = function(type){
		page_header.removeClass('header-'+state).addClass('header-'+type);
		_changeState(type);
	};
	
	var hide = function(){
		if(state === 'hide'){
			return;
		}
		page_header.removeClass('header-'+state).addClass('header-hide');
		_changeState('hide');
	};
	
	var getState = function(){
		return state;
	};
	
	return {
		hide: hide,
		show: show,
		getState: getState
	};
})($, _, is);