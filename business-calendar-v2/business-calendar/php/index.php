<?php


error_reporting( E_ALL );




mb_language( 'ja' );
mb_internal_encoding( 'UTF-8' );




require_once( 'class.business-calendar.php' );
$business_calendar = new Business_Calendar();




if ( ! empty( $_GET['year-month'] ) ) {
	$business_calendar->get_year_month();
}




$business_calendar->calendar_table();
$business_calendar->calendar_footer();







?>