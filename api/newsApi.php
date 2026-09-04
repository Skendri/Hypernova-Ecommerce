<?php
// Purpose: Fetch top Apples headlines from NewsAPI page external website and return the JSON to the client page.

// CORS: Set the specific origin allowed to access this resource
header("Access-Control-Allow-Origin: http://localhost/Hypernova-Ecommerce");

// Tell caches that the response depends on the Origin header
header("Vary: Origin");

// loads Composer's autoloader.
require __DIR__ . '/../vendor/autoload.php';
// This creates a Dotenv object. You're telling it Look inside: config/ folder for .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

$apiKey = $_ENV['API_KEY'];

$url = 'https://newsapi.org/v2/everything?q=apple&from=2026-08-20&to=2026-08-20&sortBy=popularity&apiKey=' . $apiKey;
$ch = curl_init($url);
// $ch means cURL Handle. Think of cURL as PHP's version of JavaScript's fetch().

// Configure cURL, Setting CURLOPT_RETURNTRANSFER to true means "Don't print the response. Give it back to me as a string."
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set User Agent, Every HTTP request usually includes a User-Agent header.
curl_setopt($ch, CURLOPT_USERAGENT, 'Hypernova-Ecommerce/1.0');

// Execute the Request. contain the JSON. This is the moment the request is sent.
$response = curl_exec($ch);
curl_close($ch);
// curl_close($ch); It's similar to closing a database connection after you're done with it.

echo $response;
