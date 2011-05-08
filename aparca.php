<?php
include 'lib/Aparca/Conf.php';
include 'lib/Aparca/App.php';

// get lat/lon from url
$lat = (double) filter_input(INPUT_GET, 'lat', FILTER_SANITIZE_STRING);
$lon = (double) filter_input(INPUT_GET, 'lon', FILTER_SANITIZE_STRING);

// Prepare PDO connection
$dbh = new PDO(Aparca_Conf::getDsn(), Aparca_Conf::USERNAME, Aparca_Conf::PASSWORD, array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION));

// Start the App
$aparca = new Aparca_App($dbh);

$id = (integer) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$out = array();
try {
    if ($id == 0) {
        $aparca->seGeo($lat, $lon);
        $out = json_encode(array_values($aparca->getCloseOnes(Aparca_Conf::KM)));
    } else {
        $t = filter_input(INPUT_GET, 't', FILTER_SANITIZE_STRING);
        $out = json_encode($aparca->getOne($id, $t));
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
    //@todo do something
}

header('Content-type: application/json');
echo $out;