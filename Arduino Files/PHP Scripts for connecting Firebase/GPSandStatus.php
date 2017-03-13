<?php
require_once 'firebaseLib.php';
//firebase url and token
$url = 'https://third-year-project-84df8.firebaseio.com/';
$token = 'Q8ad8Nvin15OzS5GFmYFgq5JG9SimDim6BNX4QlH';
//get variables from request
$latitude = $_GET['latitude'];
$longitude = $_GET['longitude'];
//$regNo = $_GET['regNo'];
$user = $_GET['User'];
$vehicle = $_GET['Vehicle'];
//get date/time
date_default_timezone_set('Europe/Dublin');
$date = date('d-m-Y H:i:s');
//concatenate the whole lot
$concat = $latitude . '/' . $longitude . '/' . $date;
//code for uploading GPS data
//$GPSPath = '/User-1/Cars/RegNum/' . $regNo . '/Coordinates';
$GPSPath = '/Users/' . $user . '/Vehicles/' . $vehicle . '/Coordinates';
$fb = new fireBase($url, $token);
$response = $fb->push($GPSPath, $concat);
//code for getting status
//$statPath = '/User-1/Cars/RegNum/' . $regNo . '/Status';
$statPath = '/Users/' . $user . '/Vehicles/' . $vehicle . '/Status';
$st = new fireBase($url, $token);
$value = $st->get($statPath);
echo $value;
sleep(2);
?>




