<!-- Starts PHP code.
Without this, the server wouldn’t know it's PHP. -->
<?php

// Loads external libraries installed with Composer
require __DIR__ . '/vendor/autoload.php';  //__DIR__ = current folder of this file
// vendor/autoload.php = file that automatically includes all dependencies
// This is what makes Dotenv work

// Creates a Dotenv object and loads the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);  // Dotenv reads your .env file
$dotenv->load(); // Loads values from .env into PHP.

$apiKey = $_ENV['API_KEY']; // Think of it as: “Get my API key and store it in a variable”

$url = "https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=" . $apiKey;

// Starts a cURL request
$ch = curl_init($url); // cURL is a tool to make HTTP requests (like a browser)
// $ch = connection handle.

// Tells cURL: “Don’t print the result directly. Give it to me as a variable.”
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Hypernova-Ecommerce/1.0');  // Sets a User-Agent header to identify the client making the request. This is important for some APIs that require it or for debugging purposes
// This is a custom User-Agent string that identifies the client as "Hypernova-Ecommerce" with version "1.0". Some APIs require a User-Agent header to identify the client making the request, and it can also be useful for debugging or analytics purposes

// Sends the request to the API and stores the response in $response
$response = curl_exec($ch);
curl_close($ch);

echo $response; // Prints the result to the browser You will see raw JSON
