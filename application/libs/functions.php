<?php

defined('ACCESS') or Error::exitApp();

#define global variables
global $today;



$today = date('Y-m-d');


function dateToString($date){
	return date('jS F Y', strtotime($date));
}

function today(){
	return date('Y-m-d');
}

function yesterday(){
	return date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));
}

function oneDayAgo($date){
	return date('Y-m-d', strtotime("-1 days", strtotime($date)));
}

function tomorrow(){
	return date('Y-m-d', strtotime("+1 days", strtotime(date('Y-m-d'))));
}

function now()
{
	# code...
	return time();
}


function timeToString($time){
	return date('g:i:s a', $time);
}

function generateId(){
	$characters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$generated_id = "";
	for( $i=1; $i<=2; $i++){
		$number = rand(0, 25);
		$generated_id .= $characters[$number];
	}
	$generated_id .= '-';

	for( $i=1; $i<=10; $i++){
		$generated_id .= rand(0, 9);;
	}
	return $generated_id;
}


function amtToInt($amt){
	$amt = rtrim($amt);
	$amt = str_replace(',', '', $amt);
	$amt = filter_var($amt, FILTER_SANITIZE_NUMBER_INT);
	return $amt;
}

function changeDateFormat($date, $oldFormat='Y-m-d', $newFormat = 'd-m-Y'){

	$dateObj = DateTime::createFromFormat($oldFormat, $date);
	return $new_date_format = $dateObj->format($newFormat);

}

function calculatePercentage($totalAmount, $subAmount){
	//P = PA * 100 /A
	return ($subAmount * 100) / $totalAmount;
}
