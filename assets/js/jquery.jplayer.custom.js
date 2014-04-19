/**
 * WolfJplayer UI
 */
 /* jshint -W062 */
var WolfjPlayerUi = WolfjPlayerUi || {},
	WolfjPlayerParams = WolfjPlayerParams || {};

WolfjPlayerUi = function ( $ ) {

	'use strict';

	return {

		playlistContainer : $( '.wolf-jplayer-playlist-container' ),
		overlay : $( '.wolf-jplayer-loader-overlay' ),
		loader : $( '.wolf-jplayer-loader' ),
		Playlist : $( '.wolf-jplayer-playlist' ),
		clock : 0,
		timer : null,
		isMobile : navigator.userAgent.match( /(iPad)|(iPhone)|(iPod)|(Android)|(PlayBook)|(BB10)|(BlackBerry)|(Opera Mini)|(IEMobile)|(webOS)|(MeeGo)/i ),

		init : function () {

			var $this = this;

			this.loaderOverlay();

			this.Playlist.find( 'span.close-wolf-jp-share' ).click ( function() {
				$( this ).parent().parent().parent().fadeOut();
			} );

			$( '.wolf-jp-share-icon' ).click( function() {
				var container = $( this ).parent().parent().parent();
				container.find( '.wolf-jp-overlay' ).fadeIn();
			} );
				

			$( '.wolf-share-jp-popup' ).click( function() {
				var popup = window.open( $( this ).attr( 'href' ), 'null', 'height=350,width=570, top=150, left=150' );
				if ( window.focus ) {
					popup.focus();
				}
				return false;
			} );

			$( window ).resize( function() {
				$this.responsive();
			} ).resize();
		},

		/**
		 * Loader
		 */
		loaderOverlay : function () {

			var $this = this;

			// timer to display the loader if loading l&st more than 1 sec
			$this.timer = setInterval( function() {
				
				$this.clock++;
				// console.log(clock);
				if ( $this.clock === 1 ) {
					$( '.wolf-jplayer-loader' ).fadeIn();
				}

				/** 
				 * If the loading time last more than 8 sec, we hide the overlay anyway 
				 * An iframe such as a video or a google map probably takes too much time to load
				 * So let's show the page
				 */
				if ( $this.clock === 8 ) {
					$this.hideOverlay();
				}
			
			}, 1000 );

		},

		/**
		 * Change the player appearence depending on window's width
		 */
		responsive : function () {

			if ( this.Playlist.length ) {

				this.Playlist.each( function() {
					var width = $( this ).width();
					
					if ( 425 > width ) {

						$( this ).addClass( 'wolf-jplayer-small-controls' );

						if ( 235 > width ) {

							$( this ).addClass( 'wolf-jplayer-very-small-controls' );

						} else {

							$( this ).removeClass( 'wolf-jplayer-very-small-controls' );
						}

					} else {
						
						$( this ).removeClass( 'wolf-jplayer-small-controls' );
					}
				} );

			}
		},

		/**
		 * Hide Overlay
		 */
		hideOverlay : function () {
			
			var $this = this;

			$this.loader.fadeOut( 'fast', function() {

				$this.overlay.fadeOut( 'slow', function() {
					clearInterval( $this.timer );
				} );
				
			} );
		},

		/**
		 * Custom Scrollbar
		 */
		customScrollbar : function () {

			if ( this.Playlist.length && WolfjPlayerParams.scrollBar === '1' ) {

				this.Playlist.each( function() {
					
					var container = $( this ).find( '.jp-playlist' );

					setTimeout( function() {
						container.mCustomScrollbar( {
							theme : 'light'
						} );
					}, 1500 );

				} );

			}

		},

		/**
		 * Page Load
		 */
		pageLoad : function() {

			var $this = this;

			if ( ! this.isMobile ) {
				this.customScrollbar();
			}

			setTimeout( function() {
				$this.hideOverlay();
			}, 1000 );

			$( window ).trigger( 'resize' );
		}
	}; // end return


}( jQuery );


;( function( $ ) {

	'use strict';
	WolfjPlayerUi.init();

	$( window ).load( function() {

		WolfjPlayerUi.pageLoad();

	} );
	
} )( jQuery );

