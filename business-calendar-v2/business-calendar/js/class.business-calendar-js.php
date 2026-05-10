<?php

class Business_Calendar_Js {
	
	// PHP public construct
	public function __construct() {
		
		$dir = dirname( $_SERVER['SCRIPT_NAME'] );
		$dir = dirname( $dir );
		
		header( 'Content-Type: application/javascript' );
		
		
		echo <<<EOM

/*--------------------------------------------------------------------------
	
	Script Name : Business Calendar
	Author : FIRSTSTEP - Motohiro Tani
	Author URL : https://www.1-firststep.com
	Create Date : 2012/10/05
	Version : 2.0.2
	Last Update : 2017/07/13
	
--------------------------------------------------------------------------*/


(function( $ ) {
	
	// function month_click
	function month_click() {
		
		var click_href       = $( this ).attr( 'href' );
		click_href           = click_href.split( /year-month=/ );
		var click_year_month = click_href[1].split( /\-/ );
		var now_year         = click_year_month[0];
		var now_month        = click_year_month[1];
		
		calendar_get( now_year, now_month );
		return false;
		
	}
	
	
	
	
	// function display_check
	function display_check() {
		
		var cr = $( 'p#business-calendar-copyright a' );
		
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
	
	
	
	
	// function calendar_get
	function calendar_get( y, m ) {
		
		$.ajax({
			type: 'GET',
			url: '{$dir}/php/index.php',
			cache: false,
			dataType: 'html',
			data: 'year-month='+ y +'-'+ m ,
			
			success: function( res ) {
				if( res.indexOf( 'first' ) !== -1 ){
EOM;
		
		
		
		
		// file_exists
		if ( file_exists( '../addon/copyright/copyright-replace.js' ) ) {
			require_once( '../addon/copyright/copyright-replace.js' );
		}
		
		
		
		
		echo <<<EOM

					$( '#business-calendar' ).html( res );
				} else {
					window.alert( 'カレンダーの取得が失敗しました。' );
				}
			},
			
			error : function( res ) {
				window.alert( 'Ajax通信が失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
			},
			
			complete: function() {
				if ( display_check() == false ) {
EOM;
		
		
		
		
		// file_exists
		if ( file_exists( '../addon/copyright/copyright-return.js' ) ) {
			require_once( '../addon/copyright/copyright-return.js' );
		}
		
		
		
		
		echo <<<EOM

					$( '#business-calendar' ).remove();
				};
			}
		});
		
	}
	
	
	
	
	// DOM
	var now_year_month = new Date();
	var now_year       = now_year_month.getFullYear();
	var now_month      = ( '0'+ ( now_year_month.getMonth() + 1 ) ).slice( -2 );
	
	calendar_get( now_year, now_month );
	
	
	$( '#business-calendar' ).on( 'click', 'li#prev a, li#next a', month_click );
	
})( jQuery );


EOM;
// PHP public construct end
		
		
		
		
	}
	
}

?>