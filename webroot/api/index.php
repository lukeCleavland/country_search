<?php
include_once('class.api.php');
include_once('class.country.php');

$api = new Api();

if(isset($_POST['search'])){
    $search = $_POST['search'];
}

if(isset($_POST['endpoint'])){
    $endpoint = $_POST['endpoint'];
}

$country = new Country($api, $endpoint, $search);

$result = $country->data();

echo json_encode($result);