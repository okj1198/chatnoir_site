<?php

class Business_Calendar_Admin_Js {
	
	// public construct
	public function __construct() {
		
		header( 'Content-Type: application/javascript' );
		
		
		echo <<<EOM
(function( $ ) {
	
	// function hidden_append
	function hidden_append( name, value, element ){
		
		$( '<input />' )
			.attr({
				type: 'hidden',
				id: name,
				name: name,
				value: value
			})
			.appendTo( element );
		
	}
	
	
	
	
	// function now_click
	function now_click() {
		
		var url = window.location.href;
		var url = url.replace( /\?.*$/g, '' );
		window.location.href = url;
		
	}
	
	
	
	
	// function logout_click
	function logout_click() {
		
		if ( window.confirm( 'ログアウトしますか？' ) ) {
			
			$.ajax({
				type: 'POST',
				url: window.location.href,
				cache: false,
				dataType: 'text',
				data: 'logout=true&javascript_action=true',
				
				success: function( res ) {
					var response = res.split( ',' );
					if( response[0] == 'logout_success' ){
						window.location.href = response[1];
					} else {
						window.alert( 'ログアウトが失敗しました。' );
						location.reload();
					}
				},
				
				error : function( res ) {
					window.alert( 'Ajax通信が失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
				}
			});
			
		}
		
	}
	
	
	
	
	// function write_click
	function write_click() {
		
		var cr_bool = display_check();
EOM;
		
		
		
		
		// file_exists
		if ( file_exists( '../addon/copyright/copyright-check.js' ) ) {
			require_once( '../addon/copyright/copyright-check.js' );
		}
		
		
		
		
		echo <<<EOM

		
		if ( window.confirm( '登録してもよろしいですか？' ) ) {
			
			hidden_append( 'javascript_action', cr_bool, $( 'form#admin-calendar p.submit' ) );
			
			$( '<div>' )
				.addClass( 'loading-layer' )
				.appendTo( 'body' )
				.css({
					'width': $( window ).width() + 'px',
					'height': $( window ).height() + 'px',
					'background': 'rgba( 0, 0, 0, 0.7 )',
					'position': 'fixed',
					'left': '0',
					'top': '0',
					'z-index': '999',
				})
				.append( '<span class="loading"></span>' );
			
			setTimeout(function(){
				
				$.ajax({
					type: $( 'form#admin-calendar' ).attr( 'method' ),
					url: $( 'form#admin-calendar' ).attr( 'action' ),
					cache: false,
					dataType: 'text',
					data: $( 'form#admin-calendar' ).serialize(),
					
					success: function( res ) {
						$( 'div.loading-layer, span.loading' ).remove();
						var response = res.split( ',' );
						if( response[0] == 'write_success' ){
							window.alert( '登録が完了しました。' );
							location.reload();
						} else {
							window.alert( '登録が失敗しました。' );
							$( 'input#javascript_action' ).remove();
						}
					},
					
					complete: function() {
						
					},
					
					error : function( res ) {
						window.alert( 'Ajax通信が失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
					}
				});
				
			}, 1000);
			
		}
		
	}
	
	
	
	
	// function display_check
	function display_check() {
		
		var cr = $( 'p#footer a' );
		
		if ( ! cr.length ) {
			cr_return = false;
		} else if ( cr.height() == 0 || cr.css( 'visibility' ) == 'hidden' ) {
			cr_return = false;
		} else if ( cr.parent( 'p' ).css( 'opacity' ) == '0' ) {
			cr_return = false;
		} else {
			cr_return = true;
		}
		
		return cr_return;
		
	}
	
	
	
	
	// DOM
	$( 'form#admin-calendar table td div' ).on({
		
		'mouseenter': function() {
			if ( $( this ).find( 'input' ).length ) {
				$( this ).css({
					'background' : 'rgba( 51, 119, 255, 0.05 )',
					'cursor' : 'pointer'
				});
			}
		},
		
		'mouseleave': function() {
			if ( $( this ).find( 'input' ).length ) {
				$( this ).css({
					'background' : 'transparent',
					'cursor' : 'default'
				});
			}
		},
		
		'click': function() {
			if ( $( this ).find( 'input' ).length ) {
				if ( $( this ).find( 'input' ).prop( 'checked' ) ) {
					$( this ).find( 'input' ).prop( 'checked', false );
					$( this ).parents( 'td' ).removeClass( 'holiday' );
				} else {
					$( this ).find( 'input' ).prop( 'checked', true );
					$( this ).parents( 'td' ).addClass( 'holiday' );
				}
			}
		}
		
	});
EOM;
		
		
		
		
		// file_exists
		if ( file_exists( '../addon/copyright/copyright-delete.js' ) ) {
			require_once( '../addon/copyright/copyright-delete.js' );
		}
		
		
		
		
		echo <<<EOM

	
	
	$( 'li#now div' ).on( 'click', now_click );
	
	$( 'li#logout div' ).on( 'click', logout_click );
	
	$( 'input#write-button' ).on( 'click', write_click );
	
	
})( jQuery );
EOM;
		
	}
	// public construct end
	
}
// class Business_Calendar_Admin_Js end

?>