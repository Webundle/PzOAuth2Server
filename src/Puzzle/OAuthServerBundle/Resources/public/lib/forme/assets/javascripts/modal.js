var Modal = (function($, _, is){
	
	"use strict";
	
	var modal = $('.modal');
	
	var state = "hide";
	
	var config = {
		size: 'modal-medium',
		autoClose: true
	};
	
	var _changeState = function(st){
		state = st;
	};
	
	var show = function(autoClose){
		config.autoClose = autoClose;
		modal.addClass('modal-show');
		state = 'show';
	};
	
	var hide = function(){
		modal.removeClass('modal-show');
		state = 'hide';
	};
	
	var getState = function(){
		return state;
	};
	
	
	// Initialize
	$('.modal-overlay').click(function(){
		if(config.autoClose === false){
			return;
		}else{
			hide();
		}
	});
	
	// Responsive for mobile
	if(is.mobile()){
		modal.addClass('modal-small');
	}
	
	return {
		hide: hide,
		show: show
	};
})($, _, is);