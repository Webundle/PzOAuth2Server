var CanvasOff = (function($, _, is) {

	"use strict";
	
	var $canvasType = $(".canvas-left"),
		appWrapper = $('.forme-container-wrapper'),
		$canvasActiviated = false;

	/**
	 * Function to open canvas Type
	 * @param  String canvasType [left|right|top|bottom]
	 */
	var show = function(){
		appWrapper.addClass('canvas-open');
	};

	/**
	 * Function to close canvas Type
	 * @param  String canvasType [left|right|top|bottom]
	 */
	var hide = function(){
		appWrapper.removeClass('canvas-open');
	};

	/**
	 * Function to open or close canvas Type
	 * @param  String canvasType [left|right|top|bottom]
	 */
	var toggle = function(){
		appWrapper.toggleClass('canvas-open');
		console.log("OK OK");
	};

	var disable = function(param){
		if(!param)
		{
			$('.canvas-off').each(function(){
				$(this).removeClass(".canvas-open");
			});
		}
	};
	
	function selectCanvasType(canvasType){
		switch( canvasType ) {

			case "left":
				$canvasType = $(".canvas-off-left");
				break;
			case "right":
				$canvasType = $(".canvas-off-right");
				break;
			case "top":
				$canvasType = $(".canvas-off-top");
				break;
			case "bottom":
				$canvasType = $(".canvas-off-bottom");
				break;
			default:
        		$canvasType = $(".canvas-off-left");
        }
	}

	
	// Initialize actions
	
	$('.overlay').on('click', function(){
		hide();
	});
	
    return { 
		//init : init,
		show : show,
		hide : hide,
		toggle : toggle
	};
    
})($, _, is);