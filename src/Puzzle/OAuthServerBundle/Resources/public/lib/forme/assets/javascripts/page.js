var page = (function() {

    var $pages = $( 'div.page' ),
    	isAnimating = false,
        endCurrPage = false,
		endNextPage = false,
		currentPage = ""; 
        animEndEventNames = {
			'WebkitAnimation' : 'webkitAnimationEnd',
			'OAnimation' : 'oAnimationEnd',
			'msAnimation' : 'MSAnimationEnd',
			'animation' : 'animationend'
		},
        // animation end event name
		animEndEventName = animEndEventNames[ Modernizr.prefixed( 'animation' ) ],
		// support css animations
		support = Modernizr.cssanimations;
     
    function init(current) {
		$pages.each( function() {
			var $page = $( this );
			$page.data( 'originalClassList', $page.attr( 'class' ) );
		});
		currentPage = current.addClass( 'page-current' );
	}

    function nextPage(option) {
        var animation = option.animation;
        
        if( isAnimating ) {
			return false;
		}
        
		isAnimating = true;
        
        var $currPage = currentPage,
			$nextPage = option.nextPage.addClass( 'page-current' ),
			outClass = '', inClass = '';
        
        switch( animation ) {

			case 1:
				outClass = 'pt-page-moveToLeft';
				inClass = 'pt-page-moveFromRight';
				break;
			case 2:
				outClass = 'pt-page-moveToRight';
				inClass = 'pt-page-moveFromLeft';
				break;
			case 3:
				outClass = 'pt-page-moveToTop';
				inClass = 'pt-page-moveFromBottom';
				break;
			case 4:
				outClass = 'pt-page-moveToBottom';
				inClass = 'pt-page-moveFromTop';
				break;
			case 5:
				outClass = 'pt-page-fade';
				inClass = 'pt-page-moveFromRight page-ontop';
				break;
			case 6:
				outClass = 'pt-page-fade';
				inClass = 'pt-page-moveFromLeft page-ontop';
				break;
			case 7:
				outClass = 'pt-page-fade';
				inClass = 'pt-page-moveFromBottom page-ontop';
				break;
			case 8:
				outClass = 'pt-page-fade';
				inClass = 'pt-page-moveFromTop page-ontop';
				break;
			case 9:
				outClass = 'pt-page-moveToLeftFade';
				inClass = 'pt-page-moveFromRightFade';
				break;
			case 10:
				outClass = 'pt-page-moveToRightFade';
				inClass = 'pt-page-moveFromLeftFade';
				break;
			case 11:
				outClass = 'pt-page-moveToTopFade';
				inClass = 'pt-page-moveFromBottomFade';
				break;
			case 12:
				outClass = 'pt-page-moveToBottomFade';
				inClass = 'pt-page-moveFromTopFade';
				break;
			case 13:
				outClass = 'pt-page-moveToLeftEasing page-ontop';
				inClass = 'pt-page-moveFromRight';
				break;
			case 14:
				outClass = 'pt-page-moveToRightEasing page-ontop';
				inClass = 'pt-page-moveFromLeft';
				break;
			case 15:
				outClass = 'pt-page-moveToTopEasing page-ontop';
				inClass = 'pt-page-moveFromBottom';
				break;
			case 16:
				outClass = 'pt-page-moveToBottomEasing page-ontop';
				inClass = 'pt-page-moveFromTop';
				break;
        }
        
        $nextPage.addClass( inClass ).on( animEndEventName, function() {
			$nextPage.off( animEndEventName );
			endNextPage = true;
			onEndAnimation( $currPage, $nextPage );
			if( endCurrPage ) {
				onEndAnimation( $currPage, $nextPage );
				console.log('inClass');
			}
		} );

        $currPage.addClass( outClass ).on( animEndEventName, function() {
			$currPage.off( animEndEventName );
			endCurrPage = true;
			if( endNextPage ) {
				onEndAnimation( $currPage, $nextPage );
			}
		} );

		if( !support ) {
			onEndAnimation( $currPage, $nextPage );
		}
    }
    
    function onEndAnimation( $outpage, $inpage ) {
		endCurrPage = false;
		endNextPage = false;
		resetPage( $outpage, $inpage );
		isAnimating = false;
	}

	function resetPage( $outpage, $inpage ) {
		$inpage.attr( 'class', $inpage.data( 'originalClassList' ) + ' page-current' );
		$outpage.attr( 'class', $outpage.data( 'originalClassList' ) );
		currentPage = $inpage;
		window.scrollTo( 0, 0 );
	}
    
    return { 
		init : init,
		nextPage : nextPage,
	};
})();