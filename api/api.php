<?php

// Set the specific origin allowed to access this resource
header("Access-Control-Allow-Origin: http://localhost/Hypernova-Ecommerce/autoload.php");

// Tell caches that the response depends on the Origin header
header("Vary: Origin");

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$apiKey = $_ENV['API_KEY'];

$url = "https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=" . $apiKey;
$ch = curl_init($url);
// $ch = connection handle.


curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Hypernova-Ecommerce/1.0');


$response = curl_exec($ch);
curl_close($ch);

echo $response;
