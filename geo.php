<?php
$mode = (integer) filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING);

function geoDecodeInverse($lat, $long) {
    $c = curl_init("http://maps.google.com/maps/api/geocode/json?language=es&latlng={$lat},{$long}&sensor=false");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $page = curl_exec($c);
    curl_close($c);
    return json_decode($page);
}

function geoDecodeInverse2($address) {
    $address = urlencode($address);
    die("http://maps.google.com/maps/api/geocode/json?language=es&address={$address}&sensor=false");
    
    $c = curl_init("http://maps.google.com/maps/api/geocode/json?language=es&address={$address}&sensor=false");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $page = curl_exec($c);
    curl_close($c);
    return json_decode($page);
}
$json = array();
switch ($mode) {
    case 0:
        $lat = (double) filter_input(INPUT_GET, 'lat', FILTER_SANITIZE_STRING);
        $lon = (double) filter_input(INPUT_GET, 'lon', FILTER_SANITIZE_STRING);
        $json = geoDecodeInverse($lat, $lon);
        break;
    case 1:
        $txt = filter_input(INPUT_GET, 'txt', FILTER_SANITIZE_STRING);
        $json = geoDecodeInverse2($txt);
        break;
}


header('Content-type: application/json');

echo json_encode($json->results[0]);